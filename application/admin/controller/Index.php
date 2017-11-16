<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Cookie;

class Index extends Controller {
    //构造函数
    public function _initialize() {
        if (!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }

    //错误方法访问
    function _empty(){ 
        $this -> redirect('Index/index');
    }

    //页面创建
    public function index() {
        $isLogin = Session::get('isLogin');

        $info = DB::name('employee')
        -> join('d_role', 'd_employee.role_id = d_role.role_id')
        -> join('d_province', 'd_employee.prov_num = d_province.prov_num')
        -> join('d_city', 'd_employee.city_num = d_city.city_num')
        -> join('d_area', 'd_employee.area_num = d_area.area_num')
        -> where('emp_id', $isLogin)
        -> field('d_role.role_name, d_employee.emp_id, d_employee.emp_reg_time, d_employee.emp_name, d_employee.emp_nickname, d_employee.emp_head_img, d_province.prov_name, d_city.city_name, d_area.area_name')
        -> find();

        $smenu = DB::name('employee')
        -> join('d_role_menu', 'd_role_menu.role_id = d_employee.role_id')
        -> join('d_smenu', 'd_smenu.sm_id = d_role_menu.sm_id')
        -> where('emp_id', $isLogin)
        -> field('d_smenu.*')
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

    //退出登录
    public function loginExit() {
        //清除关键session
        Session::delete('isLogin');

        //清除关键cookie
        Cookie::delete('isLogin');

        //跳转页面并友好提示
        $this ->success('已退出','admin/login/index',3);
    }
}
