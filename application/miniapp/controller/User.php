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
        $aera     = Request::instance()-> post('aera');
        $tel      = Request::instance()-> post('tel');
        $pwd      = Request::instance()-> post('pwd');
        $idNum    = Request::instance()-> post('idNum');
        $address  = Request::instance()-> post('address');
        $name     = Request::instance()-> post('name');
        $openid   = Request::instance()-> post('openid');
        $headImg  = Request::instance()-> post('headImg');
        $nickname = Request::instance()-> post('nickname');

        echo $prov    ;        echo ' ';
        echo $city    ;        echo ' ';
        echo $aera    ;        echo ' ';
        echo $tel     ;        echo ' ';
        echo $pwd     ;        echo ' ';
        echo $idNum   ;        echo ' ';
        echo $address ;        echo ' ';
        echo $name    ;        echo ' ';
        echo $openid  ;        echo ' ';
        echo $headImg ;        echo ' ';
        echo $nickname;        echo ' ';
        exit;

    }
} 