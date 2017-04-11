<?php

return [
    'database' => [
        'connection' => [
            'driver'    => 'pdo_mysql',
            'host'      => 'database',
            'dbname'    => 'neural',
            'user'      => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
        ],
        'orm' => [
            'auto_generate_proxies' => false,
        ],
    ],
    'websocket' => [
        'server' => [
            'bind' => '0.0.0.0',
            'port' => 8089,
        ],
    ],
    'push' => [
        'enabled' => true,
        'server' => [
            'bind' => '0.0.0.0',
            'host' => 'websocket-server',
            'port' => 5555,
        ],
    ],
    'cors' => [
        'cors.allowOrigin' => '*',
    ],
];
