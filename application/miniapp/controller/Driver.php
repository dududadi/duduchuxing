<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Driver extends Controller{
    public function index(){
        $res = Db::name('driver')
        ->select();

        echo json_encode($res);
        exit;
    }
    //获取用户openid
    public function getOpenId(){
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
        $open_id = Request::instance()->param('openid');

        $res = Db::name('driver')
            ->where('open_id',$open_id)
            ->find();

        $bt_id = $res['bt_id'];

        $res = Db::name('order_handup')
            ->alias('oh')
            ->where('oh.bt_id',$bt_id)
            ->select();
        echo json_encode($res);
        exit;
    }
    //司机接单
    public function receiveOrder(){
        //获取当前用户的openid
        $open_id = Request::instance()->post('openid');
         //获取自己的openid
        $driv_open_id = Request::instance()->post('driverOpenId');

        $driv = Db::name('driver')
            ->where('open_id',$driv_open_id)
            ->find();
        $driv_id = $driv['driv_id'];
        //改变挂起订单的状态---change未挂起to已挂起
        $res = Db::name('order_handup')
            ->where('open_id',$open_id)
            ->update(['oh_status'=>'已接单','driv_id'=>$driv_id]);


        $latitude = Request::instance()-> post('latitude');
        $longitude = Request::instance()-> post('longitude');
        //上传司机当前位置
        $data = [
            'open_id'=>$driv_open_id,
            'dl_latitude'=>$latitude,
            'dl_longitude'=>$longitude
        ];
        Db::name('driver_location')
            ->where('open_id',$driv_open_id)
            ->delete();
        Db::name('driver_location')
            ->insert($data);

        if($res)
        {
            echo 1;//修改成功
        }else{
            echo 0;//修改失败
        }
        exit;
    }
    //司机取消订单
    public function cancelOrder(){
        //获取用户的openid
        $open_id = Request::instance()->post('openid');
        //得到用户user_id
        $res = Db::name('user')
            ->where('open_id',$open_id)
            ->find();
        $user_id = $res['user_id'];
        //改变订单状态为已过期
        $update = Db::name('order_list')
            ->where('user_id',$user_id)
            ->update(['ols_id'=>2]);
        if($update){
            echo 1;//取消成功
        }else{
            echo 0;//取消失败
        }
        exit;
    }
    //司机点击已接到乘客
    public function received(){
        $open_id = Request::instance()-> post('openid');
        $driv = Db::name('driver')
            ->where('open_id',$open_id)
            ->find();
        $driv_id = $driv['driv_id'];

        $order = Db::name('order_list')
            ->alias('ol')
            ->join('driver d','ol.driv_id=d.driv_id')
            ->where('open_id',$open_id)
            ->where('ol.ols_id',1)
            ->find();
        $order_id = $order['ol_id'];

        //改变订单的状态为未过期
        $res = Db::name('order_list')
            ->where('driv_id',$driv_id)
            ->where('ols_id',1)
            ->update(['ols_id'=>2]);

        echo $order_id;
        exit;
    }
    //司机轮询获取用户位置
    public function getUserLocation(){
        //根据司机openid从订单表获取到状态为1（未接到客人）的记录的用户id，再得到user的openid
        $open_id = Request::instance()->post('openid');
        $res = Db::name('order_list')
            ->alias('ol')
            ->join('driver d','ol.driv_id=d.driv_id')
            ->where('d.open_id',$open_id)
            ->where('ols_id',1)
            ->find();
        $user_id = $res['user_id'];

        $userLocation = Db::name('user_location')
            ->alias('ul')
            ->join('user u','u.open_id=ul.open_id')
            ->where('u.user_id',$user_id)
            ->find();
        echo json_encode($userLocation);
        exit;
    }
    //接到乘客后，每五秒钟，添加路径点
    public function pushPoint(){
        $latitude = Request::instance()-> post('latitude');
        $longitude = Request::instance()-> post('longitude');
        $order_id = Request::instance()-> post('orderId');
        //由司机openid获取到订单id
        $data = [
            'dis_latitude'=>$latitude,
            'dis_longitude'=>$longitude,
            'ol_id'=>$order_id,
            'dis_time'=>date('Y-m-d H:i:s')
        ];

        Db::name('distance')
            ->insert($data);
    }
    //司机点击已到达终点
    public function arrive(){
        $order_id = Request::instance()->post('orderId');

        $res = Db::name('order_list')
            ->where('ol_id',$order_id)
            ->update(['ols_id'=>4]);
        echo $res;
        exit;
    }
    //司机查询结算结果
    public function checkOlsId(){
        $orderId = Request::instance()-> post('orderId');
        $res = Db::name('order_list')
            ->where('ol_id',$orderId)
            ->where('ols_id',5)
            ->find();
        echo $res;
        exit;
    }
    //司机对用户评分+评价
    public function comment(){
        $driverOpenId = Request::instance()-> post('driverOpenId');
        $comment = Request::instance()-> post('comment');
        $score = Request::instance()-> post('score');
        $orderId = Request::instance()-> post('orderId');

        $user = Db::name('order_list')
            ->where('ol_id',$orderId)
            ->find();
        $user_id = $user['user_id'];

        $driver = Db::name('driver')
            ->where('open_id',$driverOpenId)
            ->find();
        $driverId = $driver['driv_id'];
        $judge = false;
        Db::transaction(function() use($score,$comment,$user_id,$orderId,$driverId,&$judge){
            //更改订单状态为已评价
            $res = Db::name('order_list')
                ->where('ol_id',$orderId)
                ->find();
            $olsId = $res['ols_id'];
            if($olsId==6){
                $olsId=8;
            }else{
                $olsId=7;
            }
            $res = Db::name('order_list')
                ->where('ol_id',$orderId)
                ->update(['ols_id'=>$olsId]);
            //插入评论表
            $data = [
                'cdtu_content'=>$comment,
                'cdtu_score'=>$score,
                'user_id'=>$user_id,
                'driv_id'=>$driverId,
                'ol_id'=>$orderId,
                'cdtu_time'=>date('Y-m-d H:i:s')
            ];
            $res = Db::name('comment_dtu')
                ->insert($data);
            $judge=true;
        });
        if($judge){
            //成功
            echo 1;
        }else{
            //回滚
            echo 0;
        }
    }

    //司机是否已通过审核
    public function verify() {
        $openid = input('post.openId', '');
        if (!$openid) {
            echo 0;
        } else {
            $res = Db::name('driver')
                ->where('open_id', $openid)
                ->field('driv_status')
                ->find();
            if ($res) {
                echo 1;
            } else {
                echo 0;
            }
        }
        exit;
    }

    //修改手机号
    public function editInfo() {
        $psw = Request::instance()->post('psw');
        $tel = Request::instance()->post('tel');
        $openid = Request::instance()->post('openId');

        //手机号验证
        if (!preg_match("/^1[3|4|5|8][0-9]\d{8}$/", $tel)) {
            echo 0;
            exit;
        }
        //手机号是否注册
        $repeatTel = Db::name('driver')
            -> where('driv_tel', $tel)
            -> field('driv_tel')
            -> find();

        if ($repeatTel) {
            echo 1;
            exit;
        }
        //密码验证
        if (!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/", $psw)) {
            echo 2;
            exit;
        }

        //修改数据库手机号
        $res = Db::name('driver')
            ->where('open_id',$openid)
            ->where('driv_psw',md5($psw))
            ->update(['driv_tel' => $tel]);

        if ($res) {
            echo 10;
        } else {
            echo 11;
        }
        exit;
    }

}