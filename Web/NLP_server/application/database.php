<?php

return [
    // database type
    //'type'            => '\think\mongo\connection',
    'type'            => 'mysql',
    // database address
    'hostname'        => '127.0.0.1',
    // database name
    //'database'        => 'chiia',
    'database'        => 'NLP',
    // username
    'username'        => 'root',
    // password
    'password'        => 'root',
    // port
    'hostport'        => '3306',
    // dsn connection
    'dsn'             => '',
    // database connection parameters
    'params'          => [],
    // database charset
    'charset'         => 'utf8',
    // database prefix
    'prefix'          => 'NLP_',
    // database debug mode
    'debug'           => true,
    // database deploy mode, 0 centralization, 1 distribution
    'deploy'          => 0,
    // read and write separate, (for 0)
    'rw_separate'     => false,
    // master num after separate
    'master_num'      => 1,
    // default slave-server number
    'slave_no'        => '',
    // fields strict check examine
    'fields_strict'   => true,
    // result set type
    'resultset_type'  => 'array',
    // auto write timestamp
    'auto_timestamp'  => false,
    // default datetime format
    'datetime_format' => 'Y-m-d',
    // sql explain
    'sql_explain'     => false,
];
