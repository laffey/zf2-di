<?php
/**
 * replace as needed with the service factory relevant to main framework
 */
namespace Ei\Di;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Zend Service Manager friendly factory.
 */
class DiFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $appConfig = $serviceLocator->get('Config');
        $config = isset($appConfig['di']) ? $appConfig['di'] : array();

        //load shared DI config, if it exists
        $shared = SHARED_LIBRARY . '/' . str_replace('\\', '/', __NAMESPACE__) . '/config/config.php';
        if (file_exists($shared) && is_readable($shared)) {
            $shared = include $shared;
            $config = array_merge($shared, $config);
        }

        return new Di($config, $appConfig);
    }
}
