<?php

use think\Route;
Route::rule('','chiia/index/login');
Route::rule('chiia/index/login','chiia/index/login');
Route::rule('check','chiia/index/check');
Route::rule('chiia/check','chiia/index/check');
Route::rule('chiia/index/check/check','chiia/index/check');
Route::rule('chiia/index/login/check','chiia/index/check');

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
