<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类

class Driver extends Controller
{
    //构造函数
    public function _initialize() {
        //检查是否登录
        if (!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }


    public function lists()
    {

        if(!(input('?get.details')==null))
        {
            //司机管理页面模糊查询
            $startTime=input('get.startTime','1970-1-1 00:00:00');
            $endTime=input('get.endTime',date("Y-m-d"));
            $details=input('get.details','');

            $list = Db::name('driver')
                -> alias('d')        //给表起别名
                -> join('province p','p.prov_num = d.prov_num') //联表查询
                -> join('city c','c.city_num = d.city_num')  //联表查询
                -> join('area a','a.area_num = d.area_num')  //联表查询
                -> join('business_type b','b.bt_id = d.bt_id')  //联表查询
                /*-> where('driv_reg_time', '>', $startTime)
                -> where('driv_reg_time', '<',  date("Y-m-d", strtotime("+1 day",strtotime($endTime))))*/
                -> where('driv_id|driv_name','like','%'.$details.'%')
                -> field('driv_id,driv_name,driv_tel,driv_license_time,driv_car_reg_time,driv_reg_time,prov_name,city_name,area_name,driv_address,driv_status') //需要查询的字段
                -> paginate(10 , false , [
                    'type' => 'Hui',
                    'query' => [
                            'details'=>$details/*,
                            'startTime'=>$startTime,
                            'endTime'=>$endTime*/
                        ]
                    ]
                );
        }else{
            $list = Db::name('driver')
                -> alias('d')        //给表起别名
                -> join('province p','p.prov_num = d.prov_num') //联表查询
                -> join('city c','c.city_num = d.city_num')  //联表查询
                -> join('area a','a.area_num = d.area_num')  //联表查询
                -> join('business_type b','b.bt_id = d.bt_id')  //联表查询
                -> field('driv_id,driv_name,driv_tel,driv_license_time,driv_car_reg_time,driv_reg_time,prov_name,city_name,area_name,driv_address,driv_status') //需要查询的字段
                -> paginate(10 , false , ['type'=>'Hui',]);
        }

        $this->assign('list',$list);//向页面传值
        //查询需要显示的数据，并展示在页面上
        return $this->fetch();//渲染出司机列表的页面
    }

    public function verify()
    {
        //渲染出司机审核的页面
        return $this->fetch();
    }

    //显示修改密码页面
    public function modify_password()
    {
        $id=input('get.id','');
        $this->assign('list',$id);//向页面传值
        return $this->fetch();
    }
    //修改密码
    public function change_password()
    {
        $id=trim(input('post.id',''));
        $password=trim(input('post.password',''));

        $res = Db::name('driver')
            -> update(['driv_id'=>$id,'driv_psw'=>md5($password)]);

        if($res)
        {
            return 1;
        }else{
            return 0;
        }
    }

    //显示基本信息
    public function member_show(){

        $id=input('get.id','');
        if(!empty($id))
        {
            $list = Db::name('driver')
                -> alias('d')        //给表起别名
                -> join('province p','p.prov_num = d.prov_num') //联表查询
                -> join('city c','c.city_num = d.city_num')  //联表查询
                -> join('area a','a.area_num = d.area_num')  //联表查询
                -> join('business_type b','b.bt_id = d.bt_id')  //联表查询
                -> where('driv_id',$id)
                -> field('driv_name,
                        driv_tel,
                        driv_license_time,
                        driv_car_reg_time,
                        driv_reg_time,
                        prov_name,
                        city_name,
                        area_name,
                        driv_address,
                        driv_status,
                        driv_id_num,
                        driv_score,
                        driv_head_img,
                        bt_name,
                        driv_car_num,
                        driv_car_type,
                        driv_money') //需要查询的字段
                -> find();
            $this->assign('list',$list);//向页面传值
        }

        return $this->fetch();
    }

    //用户锁定
    public function member_stop()
    {
        $uid=trim(input('post.uid',''));
        $res = Db::name('driver')
            ->where('driv_id',$uid)
            ->setField('driv_status','锁定');
        return $res;
    }
    //用户使用
    public function member_start()
    {
        $uid=trim(input('post.uid',''));
        $res = Db::name('driver')
            ->where('driv_id',$uid)
            ->setField('driv_status','使用');
        return $res;
    }

    //所有用户锁定
    public function member_stop_all()
    {
        $arr=json_decode(input('post.uid',''));
        foreach($arr as $vul)
        {
            $res = Db::name('driver')
                ->where('driv_id',$vul)
                ->setField('driv_status','锁定');
        }

        return $res;
    }
    //所有用户使用
    public function member_start_all()
    {
        $arr=json_decode(input('post.uid',''));
        foreach($arr as $vul)
        {
            $res = Db::name('driver')
                ->where('driv_id',$vul)
                ->setField('driv_status','使用');
        }

        return $res;
    }

}