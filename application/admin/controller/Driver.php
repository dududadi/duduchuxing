<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类

class Driver extends Controller
{
    public function lists()
    {
        //渲染出司机列表的页面
        return $this->fetch();
    }

    public function verify()
    {
        //渲染出司机审核的页面
        return $this->fetch();
    }

}