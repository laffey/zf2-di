<?php
/**
 * Example of an application di config
 */

return array(
        'emailer' => array(
                'class' => '\Ei\Application\Service\Emailer',
                'dependencies' => array(
                        array('type' => 'setting', 'value' => 'emails', 'required' => true),
                        array('type' => 'service', 'value' => 'logger', 'required' => true),
                )
        ),
        'cache_manager' => array(
                'class' => '\Ei\Cache\CacheManager',
                'dependencies' => array(
                        array('type' => 'setting', 'value' => 'redis::namespace', 'required' => true),
                        array('type' => 'setting', 'value' => 'redis::host', 'required' => false, 'default' => '127.0.0.1'),
                        array('type' => 'setting', 'value' => 'redis::port', 'required' => false, 'default' => 6379),
                        array('type' => 'setting', 'value' => 'redis::database_index', 'required' => false, 'default' => 0),
                )
        ),
        'client_manager' => array(
                'class' => '\Ei\Application\Service\ClientManager',
                'dependencies' => array(
                    array('type' => 'object', 'value' => 'hapi_proxy'),
                    array('type' => 'service', 'value' => 'logger', 'required' => true)
                )
        ),
        'document_factory' => array(
                'class' => '\Ei\Model\Document\DocumentFactory',
                'dependencies' => array()
        ),
        'document_broker' => array(
                'class' => '\Ei\Document\DocumentBroker',
                'dependencies' => array(
                    array('type' => 'service', 'value' => 'doctrine.documentmanager.odm_default', 'required' => true),
                    array('type' => 'setting', 'value' => 'doctrine::connection::odm_default::dbname', 'required' => true)
                )
        ),
        'job_manager' => array(
            'class' => '\Ei\Job\JobManager',
            'dependencies' => array(
                array('type' => 'object', 'value' => 'mongo_job_queue', 'required' => true),
                array('type' => 'object', 'value' => 'notification_manager', 'required' => true)
            )
        ),
        'mongo_job_queue' => array(
            'class' => '\Ei\Job\QueueAdapter\Mongo',
            'dependencies' => array(
                array('type' => 'object', 'value' => 'document_broker', 'required' => true),
            )
        ),
        'notification_manager' => array(
            'class' => '\Ei\Notification\NotificationManager',
            'dependencies' => array(
                array('type' => 'object', 'value' => 'document_broker', 'required' => true),
            )
        ),
        'hapi_client' => array(
                'class' => '\Ei\Plugin\Hapi\HapiClient',
                'dependencies' => array(
                        array('type' => 'setting', 'value' => 'hapi_endpoint', 'required' => true),
                        array('type' => 'object', 'value' => 'method_manager'),
                        array('type' => 'setting', 'value' => 'hapi_ssl_version', 'required' => false, 'default' => 0),
                        array('type' => 'setting', 'value' => 'hapi_verify_peer', 'required' => false, 'default' => 1),
                        array('type' => 'setting', 'value' => 'hapi_verify_host', 'required' => false, 'default' => 2),
                        array('type' => 'service', 'value' => 'logger', 'required' => true),
                )
        ),
        'permission_manager' => array(
                'class' => '\Ei\Application\Service\Hapi\PermissionManager',
                'dependencies' => array()
        ),
        'router' => array(
                'class' => '\Ei\Application\Service\Router',
                'dependencies' => array(
                        array('type' => 'object', 'value' => 'hapi_proxy'),
                        array('type' => 'object', 'value' => 'document_broker'),
                )
        ),
        'device_type_lookup' => array(
                'class' => '\Ei\Application\Service\DeviceTypeLookup',
                'dependencies' => array(
                        array('type' => 'object', 'value' => 'document_broker'),
                        array('type' => 'service', 'value' => 'logger', 'required' => true)
                )
        ),
        'decorator_factory' => array(
                'class' => '\Ei\Model\Hapi\DeviceDecorator\DecoratorFactory',
                'dependencies' => array (
                        array ('type' => 'object', 'value' => 'device_type_lookup')
                )
        ),
        'list_repository' => array(
                'class' => '\Ei\Model\ValueObject\ListRepository',
                'dependencies' => array()
        ),
        'method_manager' => array(
                'class' => '\Ei\Plugin\Hapi\MethodManager',
                'dependencies' => array(
                        array('type' => 'object', 'value' => 'hapi_model_factory'),
                        array('type' => 'object', 'value' => 'hapi_collection_factory'),
                        array('type' => 'service', 'value' => 'logger', 'required' => true),
                )
        ),
        'user_device' => array(
                'class' => '\Ei\Application\Service\UserDevice',
                'dependencies' => array()
        ),
        'facility_manager' => array(
                'class' => '\Ei\Application\Service\FacilityManager',
                'dependencies' => array(
                        array('type' => 'object', 'value' => 'hapi_proxy'),
                        array('type' => 'object', 'value' => 'cache_manager'),
                ),
        ),
        'price_list' => array(
                'class' => '\Ei\Application\Service\Pricing\PriceList',
                'dependencies' => array(
                        array('type' => 'object', 'value' => 'hapi_proxy'),
                        array('type' => 'object', 'value' => 'cache_manager'),
                ),
        ),
);
