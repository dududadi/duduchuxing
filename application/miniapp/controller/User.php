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
    //curl请求获取openid
    public function getOpenId(){
        $appid = 'wx870f25b8a2a98f0b';
        $secret = 'd51063fe3c3b3f30688c74f1f86ab768';
        $js_code = Request::instance()-> post('code');
        $data = [];//post
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

    //用户注册验证
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

    //用户发起订单--实时监听
    public function checkHandUp(){
        $openid = Request::instance()-> post('openid');
        $start = Request::instance()-> post('start');
        $end = Request::instance()-> post('end');
        $startLongitude = Request::instance()-> post('startLongitude');
        $startLatitude = Request::instance()-> post('startLatitude');
        $endLongitude = Request::instance()-> post('endLongitude');
        $endLatitude = Request::instance()-> post('endLatitude');
        $carType = Request::instance()-> post('carType');
        $createTime = date('Y-m-d H:i:s');
       
        //获取用户当前经纬度
        $myLatitude = Request::instance()-> post('myLatitude');
        $myLongitude = Request::instance()-> post('myLongitude');

        $data = [
            'open_id'=>$openid,
            'ul_latitude'=>$myLatitude,
            'ul_longitude'=>$myLongitude
        ];
        Db::name('user_location')
            ->where('open_id',$openid)
            ->delete();
        Db::name('user_location')
            ->insert($data);


        //查看用户订单是否已挂起
        $res = Db::name('order_handup')
            ->where('open_id',$openid)
            ->find();
            /*echo count($res);
            exit;*/
        //已挂起
        if(count($res)!=0){
            //挂起的订单是否有司机接单
            //未接单

            if($res['oh_status']=='未接单'){
                //挂起的订单是否超时---3分钟        
                if(strtotime($res['oh_create_time'])+180>strtotime('now')){
                    //echo(strtotime('now'));
                    echo '{"status_code":"0"}'; //未超时
                    exit;
                }else{
                    echo '{"status_code":"1"}'; //超时

                    $res = Db::name('order_handup')
                        ->where('open_id',$openid)
                        -> delete();

                    exit;
                }
            }
            //已接单
            if($res['oh_status']=='已接单'){
                //删除挂起的订单，添加入订单表
                //从数据库读出当前用户的id
                $res2 = Db::name('user')
                    ->where('open_id',$openid)
                    -> find();
                $user_id = $res2['user_id'];

                $data = [
                'user_id'=>$user_id,
                'driv_id'=>$res['driv_id'],
                'ol_start_time'=>date('Y-m-d H:i:s') ,
                'ol_end_time'=>'',
                'rpt_id'=>1,
                'ols_id'=>1,
                'ol_km_num'=>100,
                'ol_km_price'=>100,
                'ol_overtime_price'=>0,
                'ol_tip'=>100,

                'bt_id'=>$res['bt_id'],
                'oh_start_name'=>$start,
                'oh_end_name'=>$end,
                'oh_start_longitude'=>$startLongitude,
                'oh_start_latitude'=>$startLatitude,
                'oh_end_longitude'=>$endLongitude,
                'oh_end_latitude'=>$endLatitude,
                ];

                $insert = Db::name('order_list')
                ->insert($data);
                //生成订单成功--删除挂起订单
                $res3 = Db::name('order_handup')
                ->where('open_id',$openid)
                -> delete();
                 //返回司机的信息给乘客
                echo '{"status_code":"2","driv_id":"'.$res['driv_id'].'"}';
                exit;
            }
            
        }
        //未挂起
        else{
            //获取运营类型id
            $res = Db::name('business_type')
                    ->where('bt_name',$carType)
                    -> find();
            $bt_id = $res['bt_id'];
            $data = [
                'open_id'=>$openid,
                'oh_start_name'=>$start,
                'oh_end_name'=>$end,
                'oh_start_longitude'=>$startLongitude,
                'oh_start_latitude'=>$startLatitude,
                'oh_end_longitude'=>$endLongitude,
                'oh_end_latitude'=>$endLatitude,
                'bt_id'=>$bt_id,
                'oh_create_time'=>date('Y-m-d H:i:s'),
                'oh_status'=>'未接单'
            ];
            $result = Db::name('order_handup')
            ->insert($data);
            echo '{"status_code":"3"}';//成功挂起订单行程
            exit;
        }
    }
    //取消订单
    public function rmOrder() {
        $openid = Request::instance()-> post('openid');

        $res = Db::name('order_handup')
            ->where('open_id',$openid)
            -> delete();
        if($res){
            echo 1;//成功取消
        }else{
            echo 0;//取消失败
        }
        exit;
    }
    //司机接单后，跳转页面的默认操作，用户获取接单司机的信息
    public function getDriverLocation(){
        $openid = Request::instance()-> post('openid');
        $latitude = Request::instance()-> post('latitude');
        $longitude = Request::instance()-> post('longitude');
        $driverid = Request::instance()-> post('driverid');
        $order_id = Request::instance()-> post('orderId');

        //获取订单状态
        $ols = Db::name('order_list')
            ->where('ol_id',$order_id)
            ->find();
        $ols_id = $ols['ols_id'];

        //将用户位置更新
        $data = [
            'open_id'=>$openid,
            'ul_latitude'=>$latitude,
            'ul_longitude'=>$longitude
        ];
        Db::name('user_location')
            ->where('open_id',$openid)
            ->delete();
        Db::name('user_location')
            ->insert($data);

        //获取司机位置
        $driverLocation = Db::name('driver_location')
            ->alias('dl')
            ->join('driver d','d.open_id=dl.open_id')
            ->where('d.driv_id', $driverid )
            ->find();
        $driverLocation['ols_id']=$ols_id;
        echo json_encode($driverLocation);
        exit;
    }
    public function getOrderId(){
        $openid = Request::instance()-> post('openid');
        $driverid = Request::instance()-> post('driverid');

        $user = Db::name('user')
            ->where('open_id',$openid)
            ->find();
        $user_id = $user['user_id'];
        //获取当前订单的单号
        $res = Db::name('order_list')
            ->where('user_id',$user_id)
            ->where('driv_id',$driverid)
            ->where('ols_id',1)//司机接到了乘客，状态为未过期
            ->find();
        $order_id = $res['ol_id'];
        echo $order_id;
        exit;
    }





} 


