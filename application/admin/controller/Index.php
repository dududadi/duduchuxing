<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Index extends Controller {
    public function index() {
        $isLogin = 1;

        $roleId = DB::name('employee')
        -> where('emp_id', $isLogin)
        -> field('role_id')
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
                'name' => $fmenu[$i]['fm_name'],
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
        
        $this -> assign('menuList', $menuList);
        
        return $this -> fetch();
    }

    public function welcome() {
        return $this -> fetch();
    }

    public function articleList() {
        return $this -> fetch('index/article-list');
    }

    public function aboutUs() {
        return $this -> fetch();
    }

    public function contactUs() {
        return $this -> fetch();
    }
}
