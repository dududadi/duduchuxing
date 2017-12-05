<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class Chart extends Controller {
    //构造函数
    public function _initialize() {
        if (!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }

    //用户统计
    public function user() {          
        $startDay = date('Y-m-d', strtotime(date("Y-m-d").'-29 day'));
        $startMonth = date('Y-m', strtotime(date("Y-m-d").'-5 month')).'-01';
        
        $byDayCategories = [];
        $byDayUserData = [];
        $byDayFastDriverData = [];
        $byDayTaxiDriverData = [];
        $byMonthCategories = [];
        $byMonthUserData = [];
        $byMonthFastDriverData = [];
        $byMonthTaxiDriverData = [];

        //统计最近三十天的注册人数
        for ($i =0; $i < 30; $i++) {
            $theDay = date('Y-m-d', strtotime($startDay.'+'.$i.' day'));

            $oneUser = Db::name('user')
            -> where('user_reg_time', 'like', $theDay.'%')
            -> field('count(*) num')
            -> find();

            $oneFastDriver = Db::name('driver')
            -> where('driv_reg_time', 'like', $theDay.'%')
            -> where('bt_id', 2)
            -> where('driv_status', 'neq', '未审核')
            -> field('count(*) num')
            -> find();

            $oneTaxiDriver = Db::name('driver')
            -> where('driv_reg_time', 'like', $theDay.'%')
            -> where('bt_id', 1)
            -> where('driv_status', 'neq', '未审核')
            -> field('count(*) num')
            -> find();
            
            array_push($byDayCategories, substr($theDay, 5, 5));
            array_push($byDayUserData, $oneUser['num']);
            array_push($byDayFastDriverData, $oneFastDriver['num']);
            array_push($byDayTaxiDriverData, $oneTaxiDriver['num']);
        }

        //统计最近六个月的注册人数
        for ($i =0; $i < 6; $i++) {
            $theMonth = date('Y-m', strtotime($startMonth.'+'.$i.' month'));

            $oneUser = Db::name('user')
            -> where('user_reg_time', 'like', $theMonth.'%')
            -> field('count(*) num')
            -> find();

            $oneFastDriver = Db::name('driver')
            -> where('driv_reg_time', 'like', $theMonth.'%')
            -> where('bt_id', 2)
            -> where('driv_status', 'neq', '未审核')
            -> field('count(*) num')
            -> find();

            $oneTaxiDriver = Db::name('driver')
            -> where('driv_reg_time', 'like', $theMonth.'%')
            -> where('bt_id', 1)
            -> where('driv_status', 'neq', '未审核')
            -> field('count(*) num')
            -> find();
            
            array_push($byMonthCategories, $theMonth);
            array_push($byMonthUserData, $oneUser['num']);
            array_push($byMonthFastDriverData, $oneFastDriver['num']);
            array_push($byMonthTaxiDriverData, $oneTaxiDriver['num']);
        }

        $this -> assign('byDayCategories', json_encode($byDayCategories));
        $this -> assign('byDayUserData', json_encode($byDayUserData));
        $this -> assign('byDayFastDriverData', json_encode($byDayFastDriverData));
        $this -> assign('byDayTaxiDriverData', json_encode($byDayTaxiDriverData));
        $this -> assign('byMonthCategories', json_encode($byMonthCategories));
        $this -> assign('byMonthUserData', json_encode($byMonthUserData));
        $this -> assign('byMonthFastDriverData', json_encode($byMonthFastDriverData));
        $this -> assign('byMonthTaxiDriverData', json_encode($byMonthTaxiDriverData));

        return $this -> fetch();
    }

    //订单统计
    public function orderList() {
        $startDay = date('Y-m-d', strtotime(date("Y-m-d").'-29 day'));
        $startMonth = date('Y-m', strtotime(date("Y-m-d").'-5 month')).'-01';

        $byDayCategories = [];
        $byDayFastCompletedData = [];
        $byDayFastFailedData = [];
        $byDayTaxiCompletedData = [];
        $byDayTaxiFailedData = [];
        $byMonthCategories = [];
        $byMonthFastCompletedData = [];
        $byMonthFastFailedData = [];
        $byMonthTaxiCompletedData = [];
        $byMonthTaxiFailedData = [];
        
        //统计最近三十天的订单数量
        for ($i =0; $i < 30; $i++) {
            $theDay = date('Y-m-d', strtotime($startDay.'+'.$i.' day'));

            $oneFastCompleted = Db::name('order_list')
            -> where('ol_start_time', 'like', $theDay.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 2)
            -> field('count(*) num')
            -> find();

            $oneFastFailed = Db::name('order_list')
            -> where('ol_start_time', 'like', $theDay.'%')
            -> where('ols_id', 3)
            -> where('bt_id', 2)
            -> field('count(*) num')
            -> find();

            $oneTaxiCompleted = Db::name('order_list')
            -> where('ol_start_time', 'like', $theDay.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 1)
            -> field('count(*) num')
            -> find();

            $oneTaxiFailed = Db::name('order_list')
            -> where('ol_start_time', 'like', $theDay.'%')
            -> where('ols_id', 3)
            -> where('bt_id', 1)
            -> field('count(*) num')
            -> find();

            array_push($byDayCategories, substr($theDay, 5, 5));
            array_push($byDayFastCompletedData, $oneFastCompleted['num']);
            array_push($byDayFastFailedData, $oneFastFailed['num']);
            array_push($byDayTaxiCompletedData, $oneTaxiCompleted['num']);
            array_push($byDayTaxiFailedData, $oneTaxiFailed['num']);
        }

        //统计最近六个月的订单数量
        for ($i =0; $i < 6; $i++) {
            $theMonth = date('Y-m', strtotime($startMonth.'+'.$i.' month'));

            $oneFastCompleted = Db::name('order_list')
            -> where('ol_start_time', 'like', $theMonth.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 2)
            -> field('count(*) num')
            -> find();

            $oneFastFailed = Db::name('order_list')
            -> where('ol_start_time', 'like', $theMonth.'%')
            -> where('ols_id', 3)
            -> where('bt_id', 2)
            -> field('count(*) num')
            -> find();

            $oneTaxiCompleted = Db::name('order_list')
            -> where('ol_start_time', 'like', $theMonth.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 1)
            -> field('count(*) num')
            -> find();

            $oneTaxiFailed = Db::name('order_list')
            -> where('ol_start_time', 'like', $theMonth.'%')
            -> where('ols_id', 3)
            -> where('bt_id', 1)
            -> field('count(*) num')
            -> find();
            
            array_push($byMonthCategories, $theMonth);
            array_push($byMonthFastCompletedData, $oneFastCompleted['num']);
            array_push($byMonthFastFailedData, $oneFastFailed['num']);
            array_push($byMonthTaxiCompletedData, $oneTaxiCompleted['num']);
            array_push($byMonthTaxiFailedData, $oneTaxiFailed['num']);
        }

        $this -> assign('byDayCategories', json_encode($byDayCategories));
        $this -> assign('byDayFastCompletedData', json_encode($byDayFastCompletedData));
        $this -> assign('byDayFastFailedData',  json_encode($byDayFastFailedData));
        $this -> assign('byDayTaxiCompletedData', json_encode($byDayTaxiCompletedData));
        $this -> assign('byDayTaxiFailedData', json_encode($byDayTaxiFailedData));
        $this -> assign('byMonthCategories', json_encode($byMonthCategories));
        $this -> assign('byMonthFastCompletedData', json_encode($byMonthFastCompletedData));
        $this -> assign('byMonthFastFailedData',  json_encode($byMonthFastFailedData));
        $this -> assign('byMonthTaxiCompletedData', json_encode($byMonthTaxiCompletedData));
        $this -> assign('byMonthTaxiFailedData', json_encode($byMonthTaxiFailedData));

        return $this -> fetch('order_list');
    }

    //营销统计
    public function market() {
        $startDay = date('Y-m-d', strtotime(date("Y-m-d").'-29 day'));
        $startMonth = date('Y-m', strtotime(date("Y-m-d").'-5 month')).'-01';

        $byDayCategories = [];
        $byDayFastData = [];
        $byDayTaxiData = [];
        $byMonthCategories = [];
        $byMonthFastData = [];
        $byMonthTaxiData = [];

        //统计最近三十天的收入
        for ($i =0; $i < 30; $i++) {
            $theDay = date('Y-m-d', strtotime($startDay.'+'.$i.' day'));

            $oneFast = Db::name('order_list')
            -> where('ol_start_time', 'like', $theDay.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 2)
            -> field('sum(ol_km_price) + sum(ol_time_price) sum')
            -> find();

            $oneTaxi = Db::name('order_list')
            -> where('ol_start_time', 'like', $theDay.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 1)
            -> field('sum(ol_km_price) + sum(ol_time_price) sum')
            -> find();
            
            array_push($byDayCategories, substr($theDay, 5, 5));
            array_push($byDayFastData, floatval($oneFast['sum']));
            array_push($byDayTaxiData, floatval($oneTaxi['sum']));
        }

        //统计最近六个月的收入
        for ($i =0; $i < 6; $i++) {
            $theMonth = date('Y-m', strtotime($startMonth.'+'.$i.' month'));

            $oneFast = Db::name('order_list')
            -> where('ol_start_time', 'like', $theMonth.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 2)
            -> field('sum(ol_km_price) + sum(ol_time_price) sum')
            -> find();

            $oneTaxi = Db::name('order_list')
            -> where('ol_start_time', 'like', $theMonth.'%')
            -> where('ols_id', 5)
            -> where('bt_id', 1)
            -> field('sum(ol_km_price) + sum(ol_time_price) sum')
            -> find();
            
            array_push($byMonthCategories, $theMonth);
            array_push($byMonthFastData, floatval($oneFast['sum']));
            array_push($byMonthTaxiData, floatval($oneTaxi['sum']));
        }
        
        $this -> assign('byDayCategories', json_encode($byDayCategories));
        $this -> assign('byDayFastData', json_encode($byDayFastData));
        $this -> assign('byDayTaxiData', json_encode($byDayTaxiData));
        $this -> assign('byMonthCategories', json_encode($byMonthCategories));
        $this -> assign('byMonthFastData', json_encode($byMonthFastData));
        $this -> assign('byMonthTaxiData', json_encode($byMonthTaxiData));

        return $this -> fetch();
    }
    
} 