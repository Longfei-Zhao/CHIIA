<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 15/03/2018
 * Time: 9:36 AM
 */
namespace app\chiia\controller;

use think\Controller;
use think\Request;

class Base extends Controller{
    protected $is_check_login = [''];
    public function _initialize()
    {
        if(!$this->isLogin() && (in_array(Request::instance()->action(),$this->is_check_login)||$this->is_check_login[0]=='')){
            return $this->error('Please login first','chiia/index/login');
        }
    }

    public function isLogin(){
        return session('?username');
    }
}