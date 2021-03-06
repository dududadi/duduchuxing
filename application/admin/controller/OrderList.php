<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类

class OrderList extends Controller
{
    //构造函数
    public function _initialize() {
        //检查是否登录
        if (!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }

    //渲染成交订单页面
    public function deal()
    {
        date_default_timezone_set('PRC'); //设置时区
        if(!(input('?get.details')==null))
        {
            //司机管理页面模糊查询
            if(empty(input('get.startTime')))
            {
                //传过来没有开始日期，则赋予开始日期
                $startTime='1970-1-1';
            }else{
                $startTime=input('get.startTime');
            }

            if(empty(input('get.startTime')))
            {
                //传过来没有结束日期，则赋予结束日期
                $endTime=date("Y-m-d");
            }else{
                $endTime=input('get.endTime');
            }

            $details=input('get.details','');//没有获取到值，则所有

            $list = Db::name('order_list')
                -> alias('o')        //给表起别名
                -> join('user u','u.user_id = o.user_id') //联表查询
                -> join('driver d','d.driv_id = o.driv_id')  //联表查询
                -> join('recharge_pay_type r','r.rpt_id = o.rpt_id')  //联表查询
                -> join('order_list_status os','os.ols_id = o.ols_id')  //联表查询
                -> join('business_type bt','bt.bt_id = o.bt_id')
                -> where('driv_reg_time', '>', $startTime)          //开始时间<注册时间
                -> where('driv_reg_time', '<',  date("Y-m-d", strtotime("+1 day",strtotime($endTime))))//结束时间>注册时间
                -> where('driv_id|driv_name','like','%'.$details.'%')
                -> where('ols_name','like','已支付') //筛选出成交订单
                -> field('ol_id,d.driv_id,driv_name,u.user_id,user_name,bt_name,ol_start_time,ol_end_time,ols_name') //需要查询的字段
                -> paginate(10 , false , [
                        'type' => 'Hui',
                        'query' => [
                            'details'=>$details,
                            'startTime'=>$startTime,
                            'endTime'=>$endTime
                        ]
                    ]
                );
        }else{
            $list = Db::name('order_list')
                -> alias('o')        //给表起别名
                -> join('user u','u.user_id = o.user_id') //联表查询
                -> join('driver d','d.driv_id = o.driv_id')  //联表查询
                -> join('recharge_pay_type r','r.rpt_id = o.rpt_id')  //联表查询
                -> join('order_list_status os','os.ols_id = o.ols_id')  //联表查询
                -> join('business_type bt','bt.bt_id = o.bt_id')
                -> where('ols_name','like','已支付') //筛选出成交订单
                -> field('ol_id,d.driv_id,driv_name,u.user_id,user_name,bt_name,ol_start_time,ol_end_time,ols_name') //需要查询的字段
                -> paginate(10 , false , ['type'=>'Hui']);

        }

        $this->assign('list',$list);//向页面传值
        //查询需要显示的数据，并展示在页面上
        return $this->fetch();//渲染出司机列表的页面
    }
    //渲染未成交订单页面
    public function undeal()
    {
        date_default_timezone_set('PRC'); //设置时区
        if(!(input('?get.details')==null))
        {
            //司机管理页面模糊查询
            if(empty(input('get.startTime')))
            {
                //传过来没有开始日期，则赋予开始日期
                $startTime='1970-1-1';
            }else{
                $startTime=input('get.startTime');
            }

            if(empty(input('get.startTime')))
            {
                //传过来没有结束日期，则赋予结束日期
                $endTime=date("Y-m-d");
            }else{
                $endTime=input('get.endTime');
            }

            $details=input('get.details','');//没有获取到值，则所有

            $list = Db::name('order_list')
                -> alias('o')        //给表起别名
                -> join('user u','u.user_id = o.user_id') //联表查询
                -> join('driver d','d.driv_id = o.driv_id')  //联表查询
                -> join('recharge_pay_type r','r.rpt_id = o.rpt_id')  //联表查询
                -> join('order_list_status os','os.ols_id = o.ols_id')  //联表查询
                -> join('business_type bt','bt.bt_id = o.bt_id')
                -> where('driv_reg_time', '>', $startTime)          //开始时间<注册时间
                -> where('driv_reg_time', '<',  date("Y-m-d", strtotime("+1 day",strtotime($endTime))))//结束时间>注册时间
                -> where('driv_id|driv_name','like','%'.$details.'%')
                -> where('ols_name',['like','未过期'],['like','未接客'],'or') //筛选出成交订单
                -> field('ol_id,d.driv_id,driv_name,u.user_id,user_name,bt_name,ol_start_time,ol_end_time,ols_name') //需要查询的字段
                -> paginate(10 , false , [
                        'type' => 'Hui',
                        'query' => [
                            'details'=>$details,
                            'startTime'=>$startTime,
                            'endTime'=>$endTime
                        ]
                    ]
                );
        }else{
            $list = Db::name('order_list')
                -> alias('o')        //给表起别名
                -> join('user u','u.user_id = o.user_id') //联表查询
                -> join('driver d','d.driv_id = o.driv_id')  //联表查询
                -> join('recharge_pay_type r','r.rpt_id = o.rpt_id')  //联表查询
                -> join('order_list_status os','os.ols_id = o.ols_id')  //联表查询
                -> join('business_type bt','bt.bt_id = o.bt_id')
                -> where('ols_name',['like','未过期'],['like','未接客'],'or') //筛选出成交订单
                -> field('ol_id,d.driv_id,driv_name,u.user_id,user_name,bt_name,ol_start_time,ol_end_time,ols_name') //需要查询的字段
                -> paginate(10 , false , ['type'=>'Hui']);

        }

        $this->assign('list',$list);//向页面传值

        //渲染出司机审核的页面
        return $this->fetch();
    }
    //渲染过期订单页面
    public function overdue()
    {
        date_default_timezone_set('PRC'); //设置时区
        if(!(input('?get.details')==null))
        {
            //司机管理页面模糊查询
            if(empty(input('get.startTime')))
            {
                //传过来没有开始日期，则赋予开始日期
                $startTime='1970-1-1';
            }else{
                $startTime=input('get.startTime');
            }

            if(empty(input('get.startTime')))
            {
                //传过来没有结束日期，则赋予结束日期
                $endTime=date("Y-m-d");
            }else{
                $endTime=input('get.endTime');
            }

            $details=input('get.details','');//没有获取到值，则所有

            $list = Db::name('order_list')
                -> alias('o')        //给表起别名
                -> join('user u','u.user_id = o.user_id') //联表查询
                -> join('driver d','d.driv_id = o.driv_id')  //联表查询
                -> join('recharge_pay_type r','r.rpt_id = o.rpt_id')  //联表查询
                -> join('order_list_status os','os.ols_id = o.ols_id')  //联表查询
                -> join('business_type bt','bt.bt_id = o.bt_id')
                -> where('driv_reg_time', '>', $startTime)          //开始时间<注册时间
                -> where('driv_reg_time', '<',  date("Y-m-d", strtotime("+1 day",strtotime($endTime))))//结束时间>注册时间
                -> where('driv_id|driv_name','like','%'.$details.'%')
                -> where('ols_name','like','已过期') //筛选出成交订单
                -> field('ol_id,d.driv_id,driv_name,u.user_id,user_name,bt_name,ol_start_time,ol_end_time,ols_name') //需要查询的字段
                -> paginate(10 , false , [
                        'type' => 'Hui',
                        'query' => [
                            'details'=>$details,
                            'startTime'=>$startTime,
                            'endTime'=>$endTime
                        ]
                    ]
                );
        }else{
            $list = Db::name('order_list')
                -> alias('o')        //给表起别名
                -> join('user u','u.user_id = o.user_id') //联表查询
                -> join('driver d','d.driv_id = o.driv_id')  //联表查询
                -> join('recharge_pay_type r','r.rpt_id = o.rpt_id')  //联表查询
                -> join('order_list_status os','os.ols_id = o.ols_id')  //联表查询
                -> join('business_type bt','bt.bt_id = o.bt_id')
                -> where('ols_name','like','已过期') //筛选出成交订单
                -> field('ol_id,d.driv_id,driv_name,u.user_id,user_name,bt_name,ol_start_time,ol_end_time,ols_name') //需要查询的字段
                -> paginate(10 , false , ['type'=>'Hui']);

        }

        $this->assign('list',$list);//向页面传值

        //渲染出司机审核的页面
        return $this->fetch();
    }


    //显示司机基本信息
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

    //显示用户基本信息
    public function user_show(){

        $id=input('get.id','');
        if(!empty($id))
        {
            $list = DB::name('user')
                -> alias('u')        //给表起别名
                -> join('province p', 'p.prov_num = u.prov_num')
                -> join('city c', 'c.city_num = u.city_num')
                -> join('area a', 'a.area_num = u.area_num')
                -> where('user_id',$id)
                -> field('
                user_reg_time,
                user_name,
                user_id_num,
                user_tel,
                user_score,
                user_money,
                user_status,
                user_head_img,
                user_address,
                prov_name,
                city_name,
                area_name')
                -> find();
            $this->assign('list',$list);//向页面传值
        }

        return $this->fetch();
    }

    //显示订单基本信息
    public function order_show(){

        $id=input('get.id','');
        if(!empty($id))
        {
            $list = Db::name('order_list')
                -> alias('o')        //给表起别名
                -> join('business_type b','b.bt_id = o.bt_id') //联表查询
                -> join('user u','u.user_id = o.user_id')  //联表查询
                -> join('driver d','d.driv_id = o.driv_id')  //联表查询
                -> join('recharge_pay_type r','r.rpt_id = o.rpt_id')  //联表查询
                -> join('order_list_status ols','ols.ols_id = o.ols_id')  //联表查询
                -> where('ol_id',$id)
                -> field([
                    'ol_id'=>'olID',
                    'user_name'=>'userName',
                    'driv_name'=>'drivName',
                    'ol_start_time'=>'startTime',
                    'ol_end_time'=>'endTime',
                    'rpt_name'=>'rptName',
                    'ols_name'=>'olsName',
                    'ol_km_num'=>'kmNum',
                    'ol_km_price'=>'kmPrice',
                    'ol_time_price'=>'overTimePrice',
                    'ol_tip'=>'tips',
                    'oh_start_name'=>'startName',
                    'oh_end_name'=>'endName',
                    'driv_head_img'=>'headImg'
                ])
                ->find();
            $this->assign('list',$list);//向页面传值
        }

        return $this->fetch();
    }


}