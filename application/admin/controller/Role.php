<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类

class Role extends Controller{
    //进入角色管理页面
    public function lists(){
        return $this->fetch();
    }
}