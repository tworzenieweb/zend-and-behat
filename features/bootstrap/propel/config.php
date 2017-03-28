<?php

$conf = array(
    'datasources' =>
        array(
            'bookstore' =>
                array(
                    'adapter' => 'sqlite',
                    'connection' =>
                        array(
                            'dsn' => 'sqlite::memory:',
                        ),
                ),
            'default' => 'bookstore',
        ),
    'generator_version' => '1.7.1',
);
//        $conf['classmap'] = include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classmap-myproject-conf.php');

return $conf;