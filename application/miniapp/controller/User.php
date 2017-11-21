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
        $appid = 'd51063fe3c3b3f30688c74f1f86ab768';
        $secret = '';
        $js_code = $_POST['code'];
        $data = [];

        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid .
                '&secret='.$secret.
                '&js_code='.$js_code.
                '&grant_type=authorization_code';

        echo json_encode(curlHttp($url, $data));
        exit;
    }
} 