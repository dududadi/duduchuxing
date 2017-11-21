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
            Session::set('usermaxDate', date("Y-m-d"));

            $maxDate = date("Y-m-d");
        } else {
            $maxDate = Session::get('usermaxDate');
        }

        

        if (!sessionAssist('userKey')) {
            Session::set('userKey', '');

            $keyDate = '';
        } else {
            $keyDate = Session::get('userKey');
        }   

        $userList = DB::name('user')
        -> join('d_province', 'd_province.prov_num = d_user.prov_num')
        -> join('d_city', 'd_city.city_num = d_user.city_num')
        -> join('d_area', 'd_area.area_num = d_user.area_num')
        -> where('user_reg_time', '>', $minDate)
        -> where('user_reg_time', '<',  date("Y-m-d", strtotime("+1 day",strtotime($maxDate))))
        -> where('user_name like "%'. $keyDate. '%" or user_tel like "%'. $keyDate. '%" or user_id_num like "%'. $keyDate. '%"')
        // -> whereOr('user_tel like "%'. $key. '%"')
        // -> whereOr('user_id_num like "%'. $key. '%"')
        -> field('user_id, user_reg_time, user_name, user_id_num, user_tel, user_score, user_money, user_status, user_head_img, user_address, prov_name, city_name, area_name')
        -> paginate(5);

        $this -> assign('maxDate', $maxDate);
        $this -> assign('minDate', $minDate);
        $this -> assign('keyDate', $keyDate);
        $this -> assign('userList', $userList);

        return $this -> fetch(); 
    }

    public function setCondition() {
        $minDate = Request::instance()
        -> post('minDate', '1970-1-2');
        $maxDate = Request::instance()
        -> post('maxDate', date("Y-m-d"));
        $keyDate = Request::instance()
        -> post('keyDate', '');

        Session::set('userminDate', $minDate);
        Session::set('usermaxDate', $maxDate);
        Session::set('userKey', $keyDate);

        return 0;
    }

    public function show() {
        $arr = Request::instance()
        -> param();

        $tel = $arr['tel'];

        $user = DB::name('user')
        -> join('d_province', 'd_province.prov_num = d_user.prov_num')
        -> join('d_city', 'd_city.city_num = d_user.city_num')
        -> join('d_area', 'd_area.area_num = d_user.area_num')
        -> where('user_tel', $tel)
        -> field('user_id, user_reg_time, user_name, user_id_num, user_tel, user_score, user_money, user_status, user_head_img, user_address, prov_name, city_name, area_name')
        -> find();

        $this -> assign('user', $user);

        return $this -> fetch();
    }

    public function changeStatusOne() {
        $tel = Request::instance()
        -> post('tel', '');
        $status = Request::instance()
        -> post('status', '');

        $res = Db::name('user')
        -> where('user_tel', $tel)
        -> setField('user_status', $status);

        if ($res !== false) {
            return 1;
        } else {
            return 0;
        }
    }

    public function resetPsw() {
        $psw = 'duduchuxing';
        
        $tel = Request::instance()
        -> post('tel', '');

        $res = Db::name('user')
        -> where('user_tel', $tel)
        -> setField('user_psw', md5($psw));

        if ($res !== false) {
            return 1;
        } else {
            return 0;
        }
    }

    public function unlockAll() {
        $str = Request::instance()
        -> post('list', '[]');

        $list = json_decode($str);
        $judge = false;

        Db::transaction(function() use($list, &$judge) {
            for($i = 0; $i < count($list); $i++) {
                $res = Db::name('user')
                -> where('user_tel', $list[$i])
                -> setField('user_status', '使用');
            }
            $judge = true;
        });

        if ($judge) {
            return 1;
        } else {
            return 0;
        }
    }

    public function lockAll() {
        $str = Request::instance()
        -> post('list', '[]');

        $list = json_decode($str);
        $judge = false;

        Db::transaction(function() use($list, &$judge) {
            for($i = 0; $i < count($list); $i++) {
                $res = Db::name('user')
                -> where('user_tel', $list[$i])
                -> setField('user_status', '锁定');

                if ($res === false) {
                    throw new Exception('错误原因');
                }
            }

            $judge = true;
        });

        if ($judge) {
            return 1;
        } else {
            return 0;
        }
    }
} 