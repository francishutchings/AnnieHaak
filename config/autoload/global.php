<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=annie_haak;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'home',
            ),
            array(
                'label' => 'Product Types',
                'route' => 'productTypes',
                'pages' => array(
                    array(
                        'label' => 'List',
                        'route' => 'home',
                    ),
                    array(
                        'label' => 'Add',
                        'route' => 'home',
                    ),
                    array(
                        'label' => 'Edit',
                        'route' => 'home',
                    ),
                ),
            ),
            array(
                'label' => 'Options',
                'route' => 'home',
            ),
        )
    )
);
