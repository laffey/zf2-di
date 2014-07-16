<?php
/**
 * Di
 * Our own dependency injector to use throughout the application
 *     reasons: easily customize
 *     not dependent on zf2 library
 *     improve di config loading performance
 */
namespace Ei\Di;

use \Ei\Di\Exception\DiException;

class Di
{
    
    /**
     * prevent endless injection loops that have circular dependencies
     * @var array
     */
    protected $_loadingQueue = array();
    
    /**
     * keep tabs on objects we've already init'd
     * 
     * @var array
     */
    protected $_repository = array();
    
    /**
     * di config
     * @var array
     */
    protected $_config;
    
    /**
     * loaded via zf, defined in the many config.php's
     * @var array
     */
    protected $_applicationSettings;

    /**
     * A callback which takes a string (a service name) and returns either null or an object
     * for that instance name. This is a framework-agnostic means of loading external dependencies
     * with the dependency injector.
     *
     * @var \Closure
     */
    protected $_frameworkServiceManager;

    /**
     * @param callable $cb Method that takes a string (service name) and returns an object or NULL.
     *
     * @return $this
     */
    public function setFrameworkServiceManager(\Closure $cb)
    {
        $this->_frameworkServiceManager = $cb;
        return $this;
    }
    
    /**
     * load the config.php
     *
     * @param array $config The configs for DI.
     * @param array $appConfig The app configs where dependencies are available as scalar settings.
     */
    public function __construct(array $config = array(), array $appConfig = array())
    {
        $this->_config              = $config;
        $this->_applicationSettings = $appConfig;
    }
    
    /**
     * take a key and init the appropriate class
     *     according to the config requirements
     *     
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->_repository[$key])) {
            return $this->_repository[$key];
        }
        return $this->load($key);
    }
    
    /**
     * init the required class according to its config definition
     * 
     * @param string $key
     * @return mixed
     * @throws DiException
     */
    public function load($key)
    {
        if ($this->_checkForCircularDependency($key)) {
            throw new DiException('Trying to load a class which has a circular dependency, checked for key: ' . $key, DiException::CIRCULAR_DEPENDENCY);
        }
        $this->_addToQueue($key);
        
        if (empty($this->_config[$key])) {
            throw new DiException('Di config key not found, for key: ' . $key, DiException::DI_CONFIG_KEY_NOT_FOUND);
        }
        $className = $this->_config[$key]['class'];
        if (empty($className)) {
            throw new DiException('Invalid di config format, tried on key: ' . $key, DiException::DI_CONFIG_INVALID_FORMAT);
        }
        $dependencies = array();
        if (!empty($this->_config[$key]['dependencies'])) {
            $dependencies = $this->_config[$key]['dependencies'];
        }
        $classParams = array();
        foreach ($dependencies as $dependency) {
            $value = null;
            if (!isset($dependency['type'])) {
                throw new DiException(
                    'Missing "type" key from configs',
                    DiException::DI_CONFIG_INVALID_FORMAT
                );
            }

            if ($dependency['type'] == 'setting') {
                if (empty($this->_applicationSettings)) {
                    throw new DiException('Application settings have not been loaded. Failed to load class instance.', DiException::DI_APP_CONFIG_NOT_LOADED);
                }
                
                //$dependency['value'] can access deep config settings by using :: separator
                $dependencyKeys = explode('::', $dependency['value']);
                $settings = $this->_applicationSettings;
                foreach ($dependencyKeys as $dependencyKey) {
                    if (array_key_exists($dependencyKey, $settings)) {
                        $value = $settings[$dependencyKey];
                        $settings = $value;
                    } else {
                        if ($dependency['required']) {
                            throw new DiException('Missing required application setting for "' . $dependency['value'] . '".', DiException::DI_APP_CONFIG_KEY_MISSING);
                        }
                        if (array_key_exists('default', $dependency)) {
                            $value = $dependency['default'];
                            break;
                        }
                    }
                }
            } elseif ($dependency['type'] == 'object') {
                //requires another class
                $value = $this->get($dependency['value']);
            } elseif ($dependency['type'] == 'service') {
                $cb = $this->_frameworkServiceManager;
                $value = $cb($dependency['value']);
            } else {
                throw new DiException(
                    'An invalid type was specified: ' . $dependency['type'],
                    DiException::DI_CONFIG_INVALID_FORMAT
                );
            }
            $classParams[] = $value;
        }
        
        $this->_repository[$key] = $this->_getInstance($className, $classParams);
        
        $this->_popQueue();
        return $this->_repository[$key];
    }
    
    /**
     * uses a solution similar to Zend's Di class
     * 
     * @param string $className
     * @param array $params
     * @return mixed
     */
    protected function _getInstance($className, $params = array())
    {
        // Hack to avoid Reflection in most common use cases
        switch (count($params)) {
            case 0:
                return new $className();
            case 1:
                return new $className($params[0]);
            case 2:
                return new $className($params[0], $params[1]);
            case 3:
                return new $className($params[0], $params[1], $params[2]);
            case 4:
                return new $className($params[0], $params[1], $params[2], $params[3]);
            case 5:
                return new $className($params[0], $params[1], $params[2], $params[3], $params[4]);
            default:
                $r = new \ReflectionClass($className);
        
                return $r->newInstanceArgs($params);
        }
    }
    
    /**
     * if we already queued the loading of this class, problem!
     * 
     * @param string $key
     * @return boolean
     */
    protected function _checkForCircularDependency($key)
    {
        if (in_array($key, $this->_loadingQueue)) {
            return true;
        }
        return false;
    }
    
    /**
     * queue the key
     * 
     * @param string $key
     * @return void
     */
    protected function _addToQueue($key)
    {
        array_push($this->_loadingQueue, $key);
    }
    
    /**
     * pop the queue
     *
     * @return void
     */
    protected function _popQueue()
    {
        array_pop($this->_loadingQueue);
    }

    /**
     * check if we've init'd this function yet or not
     * 
     * @return bool
     */
    public function missingApplicationSettings()
    {
        if ($this->_applicationSettings === null || !count($this->_applicationSettings)) {
            return true;
        }
        return false;
    }
    
    /**
     * _applicationSettings setter
     * 
     * @param array $applicationSettings
     * @return $this;
     */
    public function setApplicationSettings($applicationSettings)
    {
        $this->_applicationSettings = $applicationSettings;
        return $this;
    }
    
    /**
     * load the extra config
     * $subConfig is an application specific di config which will merge with the root vendor di config
     *     this is a string of the config file name, which should be located in:
     *     <project-root>/<application-name>/config/autoload/$subConfig
     *
     * @deprecated since right now
     *
     * @param string $subConfig
     * @return $this
     */
    public function setApplicaitonConfig($subConfig)
    {
        $extraConfig = include(APPLICATION_DIR . '/config/autoload/' . $subConfig);
        $this->_config = array_merge($this->_config, $extraConfig);
        return $this;
    }

}
