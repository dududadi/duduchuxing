<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类

class Role extends Controller{

    //构造函数
    public function _initialize() {
        if (!sessionAssist('isLogin')) {
            $this -> redirect('login/index');
        }
    }
    //错误方法访问
   /* function _empty(){
        $this -> redirect('Index/index');
    }*/
    //进入角色管理页面
    public function lists()
    {
        return $this->fetch();
    }

    public function admin_role_show(){
        //表连接--角色表--用户表
        $smenu = DB::name('d_role dr')
            -> join('d_employee de', 'd_employee.role_id = d_role.role_id')
            -> field('de.emp_name')
            -> select();

        $fmenu = DB::name('fmenu')
            -> select();

        $menuList = [];

        for ($i = 0; $i < count($fmenu); $i++) {
            $arr = [
                'name'  => $fmenu[$i]['fm_name'],
                'ico'   => $fmenu[$i]['fm_ico'],
                'smenu' => []
            ];

            for ($j = 0; $j < count($smenu); $j++) {
                if ($fmenu[$i]['fm_id'] == $smenu[$j]['fm_id']) {
                    array_push($arr['smenu'], $smenu[$j]);
                }
            }

            if (count($arr['smenu'])) {
                array_push($menuList, $arr);
            }
        }

        $this -> assign('info', $info);
        $this -> assign('menuList', $menuList);

        return $this -> fetch();
    }

}