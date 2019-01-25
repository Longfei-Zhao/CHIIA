<?php

return [
    // generate application public files
    '__file__' => ['common.php', 'config.php', 'database.php'],

    // define auto generating of demo module
    // (based on real filename）
    'demo'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'view'],
        'controller' => ['Index', 'Test', 'UserType'],
        'model'      => ['User', 'UserType'],
        'view'       => ['index/index'],
    ],
    // more module definition
];
