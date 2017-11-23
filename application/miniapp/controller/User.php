<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class User extends Controller {
    public function index() {
        $res = Db::name('user')
        -> select();

        echo json_encode($res);
        exit;
    }

    public function getOpenId(){
        $appid = 'wx870f25b8a2a98f0b';
        $secret = 'd51063fe3c3b3f30688c74f1f86ab768';
        $js_code = Request::instance()-> post('code');
        $data = [];
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid .
                '&secret='.$secret.
                '&js_code='.$js_code.
                '&grant_type=authorization_code';
        $res =  json_decode(curlHttp($url, $data));

        $session_key= $res->session_key;
        $openid= $res->openid;
        //是否已注册
        $res = Db::name('user')
            ->where('open_id',$openid)
            -> find();
        //数据库中没有该用户的注册信息
        if($res==null){
            $data = '{"session_key":"'.$session_key.'","open_id":"'.$openid.'","status":"fail"}';
        }else{
            $data = '{"session_key":"'.$session_key.'","open_id":"'.$openid.'","status":"success"}';
        }

        echo $data;
        exit;
    }

    public function register(){
        $prov     = Request::instance()-> post('prov');
        $city     = Request::instance()-> post('city');
        $area     = Request::instance()-> post('area');
        $tel      = Request::instance()-> post('tel');
        $pwd      = Request::instance()-> post('pwd');
        $idNum    = Request::instance()-> post('idNum');
        $address  = Request::instance()-> post('address');
        $name     = Request::instance()-> post('name');
        $openid   = Request::instance()-> post('openid');
        $headImg  = Request::instance()-> post('headImg');
        $nickname = Request::instance()-> post('nickname');

        //手机号验证
        if (!preg_match("/^1[3|4|5|8][0-9]\d{8}$/", $tel)) { 
            echo 0;
            exit;
        }

        //密码验证
        if (!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,16}$/", $pwd)) { 
            echo 1;
            exit;
        }
        
        //身份证号码验证
        if (!preg_match("/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/", $idNum)) { 
            echo 2;
            exit;
        }

        //手机号是否注册验证
        $res = Db::name('user')
        -> where('user_tel', $tel)
        -> field('user_tel')
        -> select();

        if ($res) {
            echo 3;
            exit;
        }

        //获取省份id
        $res = Db::name('province')
        -> where('prov_name', $prov)
        -> field('prov_num')
        -> find();

        $prov_num = $res['prov_num'];

        //获取城市id
        $res = Db::name('city')
        -> where('city_name', $city)
        -> where('prov_num', $prov_num)
        -> field('city_num')
        -> find();

        if ($res == 0) {
            $res = Db::name('city')
            -> where('city_name', '市辖区')
            -> where('prov_num', $prov_num)
            -> field('city_num')
            -> find();
        }

        $city_num = $res['city_num'];

        //获取地区id
        $res = Db::name('area')
        -> where('area_name', $area)
        -> where('city_num', $city_num)
        -> field('area_num')
        -> find();

        $area_num = $res['area_num'];

        //数据写入
        $res = Db::name('user')
        -> insert([
            'user_reg_time' => date("Y-m-d H:i:s"),
            'user_psw'      => md5($pwd),
            'user_name'     => $name,
            'user_id_num'   => $idNum,
            'user_tel'      => $tel,
            'user_score'    => 10,
            'user_money'    => 0,
            'user_status'   => '使用',
            'user_head_img' => $headImg,
            'user_address'  => $address,
            'prov_num'      => $prov_num,
            'city_num'      => $city_num,
            'area_num'      => $area_num,
            'open_id'       => $openid
        ]);

        if ($res !== false) {
            echo 4;
        } else {
            echo 5;
        }

        exit;
    }
} 


