<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 20/03/2018
 * Time: 4:09 PM
 */
namespace app\chiia\controller;

use think\Controller;
use think\Db;
use app\chiia\model\User;


class Index extends Controller
{
    public function login(){
        return $this->fetch();
    }

    public function check()
    {
        $data = input('post.');
        $user = New User();

        //query in mongo
//        $result = Db::table('USER')->where('username',$data['username'])->find();

//        query in mysql
        $result = Db::table('NLP_USER')->where('username',$data['username'])->find();

        if ($result){
            if($result['password'] === ($data['password']) && $result['authority'] == 1){
                session('username',$data['username']);
                session('userID',$result['userID']);
                $this->success('Login success','Main/statistic');
            }elseif($result['password'] === ($data['password']) && $result['authority'] == 2){
                session('username',$data['username']);
                session('userID',$result['userID']);
                $this->success('Login success','research/statistic');
            }
            else{
                $this->error('PASSWORD, USERNAME NOT MATCH');
            }
        }else{
            $this->error('PASSWORD, USERNAME NOT MATCH');
            exit;
        }

    }
}
