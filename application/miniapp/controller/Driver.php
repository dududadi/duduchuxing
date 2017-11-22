<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Driver extends Controller{
    public function index()
    {
        $res = Db::name('driver')
        ->select();

        echo json_encode($res);
        exit;
    }

    public function getOpenId()
    {
        $appid = 'wx0d68f07d877286f6';
        $secret = '49513cc772b525486ac51e6bf998bb77';
        $js_code = Request::instance()-> post('code');
        $data = [];
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid .
                '&secret='.$secret.
                '&js_code='.$js_code.
                '&grant_type=authorization_code';
        $res =  json_decode(curlHttp($url, $data));

        $session_key  = $res->session_key;
        $openid  = $res->openid;

        //是否已经注册
        $res = Db::name('driver')
            ->where('open_id',$openid)
            ->find();
        //数据库中没有司机的注册信息
        if($res==null){
            $data = '{"session_key":"'.$session_key.'","open_id":'.$openid.',"status":"status"}';
        }else{
            $data = '{"session_key":"'.$session_key.'","open_id":'.$openid.',"status":"success"}';
        }

        echo $data;
        exit;
    }

    public function register(){
        $region = Request::instance()->post('region/a');
        $province = $region[0];
        $city = $region[1];
        $area = $region[2];
        $psw = Request::instance()->post('psw');
        $tel = Request::instance()->post('tel');
        $cPsw = Request::instance()->post('cPsw');
        $driverName = Request::instance()->post('driverName');
        $idNum = Request::instance()->post('idNum');
        $getDate = Request::instance()->post('getDate');
        $carNum = Request::instance()->post('carNum');
        $carType = Request::instance()->post('carType');
        $carOwner = Request::instance()->post('carOwner');
        $regDate = Request::instance()->post('regDate');

        //人名正则表达式
        $realNameReg = "/^[\u4E00-\u9FA5\uf900-\ufa2d·s]{2,20}$/";

        //手机号验证
        if (!preg_match("/^1[3|4|5|8][0-9]\d{8}$/", $tel)) { 
            echo 0;
            exit;
        }
        //手机号是否注册
        $res = Db::name('driver')
        -> where('driv_tel', $tel)
        -> field('driv_tel')
        -> select();

        if ($res) {
            echo 1;
            exit;
        }
        //密码验证
        if (!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/", $pwd)) { 
            echo 2;
            exit;
        }
        //重复密码验证
        if($psw==$cPsw){
            echo 3;
            exit;
        }
        //司机姓名验证
        if(!preg_match($realNameReg, $driverName)){
            echo 4;
            exit;
        }
        //身份证号码验证
        if (!preg_match("/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/", $idNum)) { 
            echo 5;
            exit;
        }
        //初次领证日期验证
        //领证日期>3年
        $getDateThree = strtotime(substr_replace($getDate,$getDate+3,0,4));
        if($getDateThree>strtotime(date("Y-m-d")){
            //领证不足三年
            echo 6;
            exit;
        }
        //if(strtotime($getDate)+86400*365)
        //车牌号码验证
        if(!preg_match("^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$", carNum)){
            echo 7;
            exit;
        }

        //车辆拥有人姓名验证
        if(!preg_match($realNameReg, $carOwner)){
            echo 8;
            exit;
        }
        //车辆注册日期验证
        //车龄不超过8年
        $getDateEight = strtotime(substr_replace($regDate,$regDate+8,0,4));
        if($getDateEight<strtotime(date("Y-m-d")){
            //领证超过8年
            echo 9;
            exit;
        }
    }
}