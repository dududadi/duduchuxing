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
    public function orderList()
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
                'ol_overtime_price' => 'overTimePrice',
                'ol_tip' => 'tips',
                'oh_start_name' => 'startName',
                'oh_end_name' => 'endName',
                'driv_head_img' => 'headImg'
            ])
            ->select();
        echo json_encode($res);
        exit;
    }

}