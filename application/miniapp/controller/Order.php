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
        $wxopid=input('post.wxopid','');
        $res = Db::name('order_list')
            ->alias('o')//给表起别名
            ->join('business_type b', 'b.bt_id = o.bt_id')//联表查询
            ->join('user u', 'u.user_id = o.user_id')//联表查询
            ->join('driver d', 'd.driv_id = o.driv_id')//联表查询
            ->join('recharge_pay_type r', 'r.rpt_id = o.rpt_id')//联表查询
            ->join('order_list_status ols', 'ols.ols_id = o.ols_id')//联表查询
            ->where('u.open_id',$wxopid)
            ->field([
                'user_name' => 'userName',
                'driv_name' => 'drivName',
                'ol_start_time' => 'startTime',
                'ol_end_time' => 'endTime',
                'rpt_name' => 'rptName',
                'ols_name' => 'olsName',
                'ol_km_num' => 'kmNum',
                'ol_km_price' => 'kmPrice',
                'ol_time_price' => 'overTimePrice',
                'ol_tip' => 'tips',
                'oh_start_name' => 'startName',
                'oh_end_name' => 'endName',
                'user_head_img' => 'headImg'
            ])
            ->select();
        $ruleArr = Db::name('business_type')
            ->alias('bt')
            ->join('order_list ol','ol.bt_id=bt.bt_id')
            ->join('user u','u.user_id=ol.user_id')
            ->join('rule r','r.bt_id=bt.bt_id')
            ->where('r.rl_price_type','low')
            ->where('u.open_id',$wxopid)
            ->find();
        $low = $ruleArr['rl_price'];

        for($i=0;$i<count($res);$i++){
            $kmp = $res[$i]['kmPrice'];
            $tp = $res[$i]['overTimePrice'];
            $tip = $res[$i]['tips'];
            $cost = $kmp+$tp+$tip;
            if($low>$cost){
                $cost = $low;
            }
            $res[$i]['cost']=$cost;
        }
        echo json_encode($res);
        exit;
    }

    //司机端查询历史订单
    public function dirvOrderList()
    {
        $wxopid=input('post.wxopid','');
        $res = Db::name('order_list')
            ->alias('o')//给表起别名
            ->join('business_type b', 'b.bt_id = o.bt_id')//联表查询
            ->join('user u', 'u.user_id = o.user_id')//联表查询
            ->join('driver d', 'd.driv_id = o.driv_id')//联表查询
            ->join('recharge_pay_type r', 'r.rpt_id = o.rpt_id')//联表查询
            ->join('order_list_status ols', 'ols.ols_id = o.ols_id')//联表查询
            ->where('d.open_id',$wxopid)
            ->field([
                'user_name' => 'userName',
                'driv_name' => 'drivName',
                'ol_start_time' => 'startTime',
                'ol_end_time' => 'endTime',
                'rpt_name' => 'rptName',
                'ols_name' => 'olsName',
                'ol_km_num' => 'kmNum',
                'ol_km_price' => 'kmPrice',
                'ol_time_price' => 'overTimePrice',
                'ol_tip' => 'tips',
                'oh_start_name' => 'startName',
                'oh_end_name' => 'endName',
                'driv_head_img' => 'headImg'
            ])
            ->select();
        $ruleArr = Db::name('business_type')
            ->alias('bt')
            ->join('driver d','d.bt_id=bt.bt_id')
            ->join('rule r','r.bt_id=bt.bt_id')
            ->where('r.rl_price_type','low')
            ->where('d.open_id',$wxopid)
            ->find();
        $low = $ruleArr['rl_price'];

        for($i=0;$i<count($res);$i++){
            $kmp = $res[$i]['kmPrice'];
            $tp = $res[$i]['overTimePrice'];
            $tip = $res[$i]['tips'];
            $cost = $kmp+$tp+$tip;
            if($low>$cost){
                $cost = $low;
            }
            $res[$i]['cost']=$cost;
        }
        echo json_encode($res);
        exit;
    }

}