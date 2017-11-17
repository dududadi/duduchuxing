<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class User extends Controller {
    //构造函数
    public function _initialize() {
        if (!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }

    public function lists() {
        if (!sessionAssist('userminDate')) {
            Session::set('userminDate', '1970-1-1');

            $minDate = '1970-1-1';
        } else {
            $minDate = Session::get('userminDate');
        }
        
        if (!sessionAssist('usermaxDate')) {
            Session::set('usermaxDate', '2099-12-31');

            $maxDate = '2099-12-31';
        } else {
            $maxDate = Session::get('usermaxDate');
        }

        if (!sessionAssist('userKey')) {
            Session::set('userKey', '');

            $key = '';
        } else {
            $key = Session::get('userKey');
        }             

        $userList = DB::name('user')
        -> join('d_province', 'd_province.prov_num = d_user.prov_num')
        -> join('d_city', 'd_city.city_num = d_user.city_num')
        -> join('d_area', 'd_area.area_num = d_user.area_num')
        -> where('user_reg_time', 'gt', $minDate)
        -> where('user_reg_time', 'lt', $maxDate)
        -> where('user_name like "%'. $key. '%"')
        -> whereOr('user_tel like "%'. $key. '%"')
        -> whereOr('user_id_num like "%'. $key. '%"')
        -> field('user_id, user_reg_time, user_name, user_id_num, user_tel, user_score, user_money, user_status, user_head_img, user_address, prov_name, city_name, area_name')
        -> paginate(1);

        $this -> assign('maxDate', $maxDate);
        $this -> assign('minDate', $minDate);
        $this -> assign('keyDate', $key);
        $this -> assign('userList', $userList);

        return $this -> fetch(); 
    }

    public function setCondition() {
        $minDate = Request::instance()
        -> post('minDate', '1970-1-1');
        $maxDate = Request::instance()
        -> post('maxDate', '2099-12-31');
        $key = Request::instance()
        -> post('key', '');

        Session::set('userminDate', $minDate);
        Session::set('usermaxDate', $maxDate);
        Session::set('userKey', $key);

        return 0;
    }
} 