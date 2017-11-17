<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class User extends Controller {
    //构造函数
    public function _initialize() {
        if (!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }

    public function lists() {
        


        return $this -> fetch();
    }
} 