<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/11/26
 * Time: 16:18
 */

namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Order extends Controller{

    //用户端查询历史订单
    public function userOrderList()
    {
        $wxopid=input('post.wxopid','');    //微信小程序传来的开放ID
        $res = Db::name('order_list')
            ->alias('o')//给表起别名
            ->join('business_type b', 'b.bt_id = o.bt_id')//联表查询
            ->join('user u', 'u.user_id = o.user_id')//联表查询
            ->join('driver d', 'd.driv_id = o.driv_id')//联表查询
            ->join('recharge_pay_type r', 'r.rpt_id = o.rpt_id')//联表查询
            ->join('order_list_status ols', 'ols.ols_id = o.ols_id')//联表查询
            ->where('u.open_id',$wxopid)                //将微信开放ID作为凭证向数据库查询信息
            ->field([
                'user_name' => 'userName',              //用户名     将查询的字段取别名
                'driv_name' => 'drivName',              //司机名
                'd.driv_id'=>'driverId',                //司机ID
                'ol_start_time' => 'startTime',         //订单开始时间
                'ol_end_time' => 'endTime',             //订单结束时间
                'rpt_name' => 'rptName',                 //付款方式
                'ols_name' => 'olsName',                 //订单类型
                'ol_km_num' => 'kmNum',                  //公里数
                'ol_km_price' => 'kmPrice',             //里程价
                'ol_time_price' => 'overTimePrice',    //超时价格
                'ol_tip' => 'tips',                      //小费
                'oh_start_name' => 'startName',         //开始地点
                'oh_end_name' => 'endName',              //结束地点
                'user_head_img' => 'headImg',           //用户头像
                'ol_id'=>'orderId'                       //订单ID
            ])
            ->select();
        $ruleArr = Db::name('business_type')
            ->alias('bt')                                       //取别名
            ->join('order_list ol','ol.bt_id=bt.bt_id')     //联表查询 订单表
            ->join('user u','u.user_id=ol.user_id')          //联表查询 用户表
            ->join('rule r','r.bt_id=bt.bt_id')              //联表查询 计费规则表
            ->where('r.rl_price_type','low')                 //价格类型
            ->where('u.open_id',$wxopid)                      //将微信开放ID作为凭证向数据库查询信息
            ->find();
        $low = $ruleArr['rl_price'];   //获取低价

        for($i=0;$i<count($res);$i++){
            $kmp = $res[$i]['kmPrice'];         //获取里程价
            $tp = $res[$i]['overTimePrice'];    //获取时长价
            $tip = $res[$i]['tips'];            //获取小费
            $cost = $kmp+$tp+$tip;              //计算总费用
            if($low>$cost){
                $cost = $low;                   //低于最低价取最低价
            }
            $res[$i]['cost']=$cost;             //得出最后费用
        }
        echo json_encode($res);             //转成JSON才能传回页面
        exit;
    }

    //司机端查询历史订单
    public function dirvOrderList()
    {
        $wxopid=input('post.wxopid','');    //微信小程序传来的开放ID
        $res = Db::name('order_list')
            ->alias('o')//给表起别名
            ->join('business_type b', 'b.bt_id = o.bt_id')//联表查询
            ->join('user u', 'u.user_id = o.user_id')//联表查询
            ->join('driver d', 'd.driv_id = o.driv_id')//联表查询
            ->join('recharge_pay_type r', 'r.rpt_id = o.rpt_id')//联表查询
            ->join('order_list_status ols', 'ols.ols_id = o.ols_id')//联表查询
            ->where('d.open_id',$wxopid)                //将微信开放ID作为凭证向数据库查询信息
            ->field([
                'user_name' => 'userName',              //用户名     将查询的字段取别名
                'driv_name' => 'drivName',              //司机名
                'ol_start_time' => 'startTime',         //订单开始时间
                'ol_end_time' => 'endTime',             //订单结束时间
                'rpt_name' => 'rptName',                 //付款方式
                'ols_name' => 'olsName',                 //订单类型
                'ol_km_num' => 'kmNum',                  //公里数
                'ol_km_price' => 'kmPrice',             //里程价
                'ol_time_price' => 'overTimePrice',    //超时价格
                'ol_tip' => 'tips',                      //小费
                'oh_start_name' => 'startName',         //开始地点
                'oh_end_name' => 'endName',              //结束地点
                'driv_head_img' => 'headImg',            //司机头像
                'ol_id'=>'orderId'                       //订单ID
            ])
            ->select();
        $ruleArr = Db::name('business_type')
            ->alias('bt')
            ->join('driver d','d.bt_id=bt.bt_id')     //联表查询 订单表
            ->join('rule r','r.bt_id=bt.bt_id')         //联表查询 计费规则表
            ->where('r.rl_price_type','low')            //价格类型
            ->where('d.open_id',$wxopid)                //将微信开放ID作为凭证向数据库查询信息
            ->find();
        $low = $ruleArr['rl_price'];   //获取低价

        for($i=0;$i<count($res);$i++){
            $kmp = $res[$i]['kmPrice'];         //获取里程价
            $tp = $res[$i]['overTimePrice'];    //获取时长价
            $tip = $res[$i]['tips'];            //获取小费
            $cost = $kmp+$tp+$tip;              //计算总费用
            if($low>$cost){
                $cost = $low;                   //低于最低价取最低价
            }
            $res[$i]['cost']=$cost;             //得出最后费用
        }
        echo json_encode($res);
        exit;
    }

}