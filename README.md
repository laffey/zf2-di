zf2-di
======

Alternative, slimmed down dependency injector for Zend Framework 2

The default dependency injector (di) for ZF2 is a bit heavy, as well as tightly coupled with the framework. I wanted to create a very simple-to-use, slim, fast, portable injector to use in multiple projects.

Allows injecting of:
   * config values
   * other objects initialized by the injector
   * other objects or services initialized elsewhere in the application

Allows for nested config settings.

Includes a required flag, allowing optional dependencies.

Prevents, and throws exeption on circular dependencies.

======================================================================================

Nested config example:

```
//app-config.php
return array(
        'doctrine' => array(
            'configuration' => array(
                'odm_default' => array(
                    'driver'             => 'odm_default',
                    'metadata_cache'     => 'array',

                    'generate_proxies'   => true,
                    'proxy_dir'          => APPLICATION_DIR . '/data/Doctrine/Proxy',
                    'proxy_namespace'    => 'Doctrine\Proxy',

//...

// would look like this in di config.php:
'document_broker' => array(
                'class' => '\Ei\Document\DocumentBroker',
                'dependencies' => array(
                    array('type' => 'setting', 'value' => 'doctrine.configuration.odm_default.generate_proxies', 'required' => true),
                    )
                    ),

//...
```
