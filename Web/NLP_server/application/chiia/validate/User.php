<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 14/03/2018
 * Time: 9:20 PM
 */

namespace app\chiia\validate;

use think\Validate;

class User extends Validate{
    protected $rule = [
        'username' => 'require|min:3',
        'password' => 'require|min:6|confirm:repassword',
        'email' => 'require',
        'phone' => 'require',
    ];

    protected $message = [
        'username.require' => 'username cannot be null',
        'username.min' => 'username length must more than 3',
        'password.require' => 'password cannot be null',
        'password.min' => 'password length must more than 6',
        'password.confirm' => 'Inconsistent input twice ',
        'email'=>'email cannot be null',
        'phone'=> 'phone cannot be null',
    ];
}