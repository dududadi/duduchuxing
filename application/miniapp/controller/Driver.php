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
            $data = '{"session_key":"'.$session_key.'","open_id":"'.$openid.'","status":"fail"}';
        }else{
            $data = '{"session_key":"'.$session_key.'","open_id":"'.$openid.'","status":"success"}';
        }
        echo $data;
        exit;
    }

    //司机注册
    public function register(){
        //$region = Request::instance()->post('region/a');
        //$province = $region[0];
        //$city = $region[1];
        //$area = $region[2];
        $province = Request::instance()->post('province');
        $city = Request::instance()->post('city');
        $area = Request::instance()->post('area');
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
        $btName = Request::instance()->post('btName');
        $address = Request::instance()->post('address');
        $openid = Request::instance()->post('openid');
        $headImg = Request::instance()->post('headImg');
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
        if (!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/", $psw)) { 
            echo 2;
            exit;
        }
        //重复密码验证
        if($psw!=$cPsw){
            echo 3;
            exit;
        }

        //人名正则表达式
        $realNameReg = "/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$|^[a-zA-Z0-9\x{4e00}-\x{9fa5}][a-zA-Z0-9_\s\ \x{4e00}-\x{9fa5}\.]*[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u";

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
        if($getDateThree>strtotime(date("Y-m-d"))){
            //领证不足三年

            echo 6;
            exit;
        }

        //if(strtotime($getDate)+86400*365)
        //车牌号码验证
        if(!preg_match("/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}$/u",$carNum)){
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
        if($getDateEight<strtotime(date("Y-m-d"))){
            //领证超过8年
            echo 9;
            exit;
        }

        //获取省份id
        $res = Db::name('province')
        -> where('prov_name', $province)
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


        //获取运营类型id
        $btIdArray = Db::name('business_type')
        -> where('bt_name', $btName)
        -> field('bt_id')
        -> find();

        $bt_id = $btIdArray['bt_id'];
        $psw = md5($psw);
        $data = [
            'driv_psw'=>$psw,
            'driv_reg_time' => date("Y-m-d H:i:s"),
            'prov_num'      => $prov_num,
            'city_num'      => $city_num,
            'area_num'      => $area_num,
            'driv_address'  => $address,
            'driv_name'     => $driverName,
            'driv_id_num'   => $idNum,
            'driv_license_time'=> $getDate,
            'driv_car_num'    => $carNum,
            'driv_car_type'    => $carType,
            'driv_owner'    => $carOwner,
            'driv_car_reg_time'=>$regDate,
            'driv_money'=>0,
            'driv_tel'=>$tel,
            'driv_status'   => '使用',
            'driv_score'=>10,
            'driv_head_img' => $headImg,
            'bt_id'=>$bt_id,
            'driv_bank_num'=>'',
            'open_id'       => $openid,
            'driv_head_img'=> $headImg
        ];
        //数据写入
        $res = Db::name('driver')
        -> insert($data);

        if ($res !== false) {
            echo 10;
        } else {
            echo 11;
        }
        //dump($data);
        exit;
    }

    //获取运营类型下拉框
    public function getBtName(){
        $res = Db::name('business_type')
        ->select();
        $data = [];
        foreach ($res as  $value) {
            array_push($data, $value['bt_name']);
        }
        echo json_encode($data);
        exit;
    }
    //司机获取当前挂起订单的信息
    public function getOrderList(){
        //获取司机的openid--得到司机的运营类型id
        $open_id = Request::instance()->post('openid');

        $res = Db::name('driver')
            ->where('open_id',$open_id)
            ->find();
        echo json_encode($open_id);
        exit;
        echo json_encode($res);
        
        $bt_id = $res['bt_id'];

        $res = Db::name('order_handup')
            ->alias('oh')
            ->join('driver d','oh.driv_id=d.driv_id')
            ->where('d.bt_id',2)
            ->select();
        echo json_encode($res); 
    }

    public function receveOrder(){
        $open_id = Request::instance()->post('openid');
        $res = Db::name('driver')
            ->where('open_id',$open_id)
            ->find();
        $bt_id = $res['bt_id'];
        $res = Db::name('order_handup')
            ->alias('oh')
            ->join('driver d','oh.driv_id=d.driv_id')
            ->where('d.bt_id',$bt_id)
            ->select();
        echo json_encode($res);
    }
    //司机接单


    
}