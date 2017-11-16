<?php
namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{
    public function login() {
        return $this -> fetch('index/login');
    }



    public function checkUser()
    {
        if(captcha_check($captcha)){
            //验证成功


        }else{
            //验证失败

            return $this -> fetch('index/login');
        };


    }
}