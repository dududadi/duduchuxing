<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Monitor extends Controller {
    //构造函数
    public function _initialize() {
        if (!sessionAssist('isLogin')) {
            $this->redirect('Login/index');
        }
    }

    //显示司机正在进行的订单
    public function driver() {
        $getProvince = input('post.province','');
        $getCity = input('post.city','');
        $getArea = input('post.area','');

        if ($getProvince && !$getCity && !$getArea) {
            $this->assign('alert','1');
            return $this->fetch();
        }

        if ($getCity) {
            $city = ['t4.city_num' => ['=',$getCity]];
        } else {
            $city = '1 = 1';
        }

        if ($getArea) {
            $area = ['t4.area_num' => ['=',$getArea]];
        } else {
            $area = '1 = 1';
        }

        //查询正在进行的订单数据
        $data = Db::name('order_list')
        ->alias('t1')
        ->join('d_business_type t2','t1.bt_id = t2.bt_id')
        ->join('d_user t3','t3.user_id = t1.user_id')
        ->join('d_driver t4','t4.driv_id = t1.driv_id')
        ->join('d_order_list_status t5','t5.ols_id = t1.ols_id')
        ->where('t1.ols_id',['=',2],['=',4],'or')
        ->where($area)
        ->where($city)
        ->order('t1.ol_start_time desc')
        ->field('t1.ol_id,t1.user_id,t1.driv_id,t1.ol_start_time,t1.ol_km_num,t1.ol_km_price,t1.ol_time_price,t1.ol_tip,t1.oh_start_latitude,t1.oh_start_longitude,t1.oh_end_latitude,t1.oh_end_longitude,t1.oh_start_name,t1.oh_end_name,t2.bt_name,t5.ols_name')
            ->paginate(10 , false , ['type'=>'Hui']);
        $this->assign('list',$data); //绑定列表数据
        $this->assign('alert','0');
        return $this->fetch();
    }

    //显示已完成的订单
    public function history() {
        $getProvince = input('post.province','');
        $getCity = input('post.city','');
        $getArea = input('post.area','');

        if ($getProvince && !$getCity && !$getArea) {
            $this->assign('alert','1');
            return $this->fetch();
        }

        if ($getCity) {
            $city = ['t4.city_num' => ['=',$getCity]];
        } else {
            $city = '1 = 1';
        }

        if ($getArea) {
            $area = ['t4.area_num' => ['=',$getArea]];
        } else {
            $area = '1 = 1';
        }

        //查询已完成的订单数据
        $data = Db::name('order_list')
        ->alias('t1')
        ->join('d_business_type t2','t1.bt_id = t2.bt_id')
        ->join('d_user t3','t3.user_id = t1.user_id')
        ->join('d_driver t4','t4.driv_id = t1.driv_id')
        ->join('d_order_list_status t5','t5.ols_id = t1.ols_id')
        ->where('t1.ols_id',5)
        ->where($area)
        ->where($city)
        ->order('t1.ol_start_time desc')
        ->field('t1.ol_id,t1.user_id,t1.driv_id,t1.ol_start_time,t1.ol_km_num,t1.ol_km_price,t1.ol_time_price,t1.ol_tip,t1.oh_start_latitude,t1.oh_start_longitude,t1.oh_end_latitude,t1.oh_end_longitude,t1.oh_start_name,t1.oh_end_name,t2.bt_name,t5.ols_name')
            ->paginate(10 , false , ['type'=>'Hui']);
        $this->assign('list',$data); //绑定列表数据
        $this->assign('alert','0');
        return $this->fetch();
    }

    //显示详细路径
    public function details() {
        $getOrderId = input('get.id','');
        $getStartName = input('get.sName','');
        $getEndName = input('get.eName','');
        if (!$getOrderId) {
            return '<h1>请求出错！请联系管理员！</h1>';
        } else {
            $data = Db::name('distance')
            ->alias('t1')
            ->join('d_order_list t2','t1.ol_id = t2.ol_id')
            ->where('t1.ol_id',$getOrderId)
            ->order('t1.dis_time asc,t1.dis_id asc')
            ->field('dis_longitude,dis_latitude')
            ->select();
            $data = json_encode($data);
            $this->assign('data',$data);
            $this->assign('sName',$getStartName);
            $this->assign('eName',$getEndName);
            return $this->fetch();
        }
    }

    //显示乘客正在进行的订单
    public function user() {
        $getProvince = input('post.province','');
        $getCity = input('post.city','');
        $getArea = input('post.area','');

        if ($getProvince && !$getCity && !$getArea) {
            $this->assign('alert','1');
            return $this->fetch();
        }

        if ($getCity) {
            $city = ['t4.city_num' => ['=',$getCity]];
        } else {
            $city = '1 = 1';
        }

        if ($getArea) {
            $area = ['t4.area_num' => ['=',$getArea]];
        } else {
            $area = '1 = 1';
        }

        //查询正在进行的订单数据
        $data = Db::name('order_list')
            ->alias('t1')
            ->join('d_business_type t2','t1.bt_id = t2.bt_id')
            ->join('d_user t3','t3.user_id = t1.user_id')
            ->join('d_driver t4','t4.driv_id = t1.driv_id')
            ->join('d_order_list_status t5','t5.ols_id = t1.ols_id')
            ->where('t1.ols_id',['=',2],['=',4],'or')
            ->where($area)
            ->where($city)
            ->order('t1.ol_start_time desc')
            ->field('t1.ol_id,t1.user_id,t1.driv_id,t1.ol_start_time,t1.ol_km_num,t1.ol_km_price,t1.ol_time_price,t1.ol_tip,t1.oh_start_latitude,t1.oh_start_longitude,t1.oh_end_latitude,t1.oh_end_longitude,t1.oh_start_name,t1.oh_end_name,t2.bt_name,t5.ols_name')
            ->paginate(10 , false , ['type'=>'Hui']);
        $this->assign('list',$data); //绑定列表数据
        $this->assign('alert','0');
        return $this->fetch();
    }

    //显示乘客位置
    public function location() {
        $getUserId = input('get.id','');
        $getStartName = input('get.sName','');
        $getEndName = input('get.eName','');
        if (!$getUserId) {
            return '<h1>请求出错！请联系管理员！</h1>';
        } else {
            $data = Db::name('user_location')
            ->alias('t1')
            ->join('d_user t2','t1.open_id = t2.open_id')
            ->where('t2.user_id',$getUserId)
            ->field('ul_longitude,ul_latitude')
            ->select();
            $data = json_encode($data);
            $this->assign('data',$data);
            $this->assign('sName',$getStartName);
            $this->assign('eName',$getEndName);
            return $this->fetch();
        }
    }

    public function getSelectVal() {
        //获取角色下拉菜单
        $roleData = db('role')->column('role_id,role_name');
        $roleData = empty($roleData)?'0':$roleData;
        //获取省份下拉菜单
        $provinceData = db('province')->column('prov_num,prov_name');
        $provinceData = empty($provinceData)?'0':$provinceData;
        $data = ['role' => $roleData,'province' => $provinceData];
        return json($data);
    }

    //获取城市下拉菜单
    public function getCity() {
        $provNum = Request::instance()->post('provNum','');
        $cityData = db('city')->where('prov_num',$provNum)->column('city_num,city_name');
        $cityData = empty($cityData)?'0':$cityData;
        $data = ['city' => $cityData];
        return json($data);
    }

    //获取区/县下拉菜单
    public function getArea() {
        $cityNum = input('post.cityNum','');
        $areaData = db('area')->where('city_num',$cityNum)->column('area_num,area_name');
        $areaData = empty($areaData)?'0':$areaData;
        $data = ['area' => $areaData];
        return json($data);
    }
}

?>
