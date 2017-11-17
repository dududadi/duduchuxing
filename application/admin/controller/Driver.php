<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类

class Driver extends Controller
{
    public function lists()
    {
        $list = Db::name('driver')
            ->alias('d')        //给表起别名
            ->join('province p','p.prov_num = d.prov_num') //联表查询
            ->join('city c','c.city_num = d.city_num')  //联表查询
            ->join('area a','a.area_num = d.area_num')  //联表查询
            ->join('business_type b','b.bt_id = d.bt_id')  //联表查询
            -> field('driv_id') //需要查询的字段
            -> paginate(10 , false , ['type'=>'Hui']);


        $this->assign('list',$list);//向页面传值
        //查询需要显示的数据，并展示在页面上
        return $this->fetch();//渲染出司机列表的页面
    }

    public function verify()
    {
        //渲染出司机审核的页面
        return $this->fetch();
    }


    public function list_show()
    {

    }


    public function member_show()
    {
        return $this->fetch('member-show');
    }

    public function lists_stop()
    {
        $uid=trim(input('post.uid',''));
    }



}