<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class Bill extends Controller {
    public function driverBill()
	{
		$openid=Request::instance()-> post('openid');
		$list = DB::name('driver')
					->alias('d')//给表起别名
			->join('driv_money_record dmr','dmr.driv_id = d.driv_id')
            ->where('d.open_id',$openid)
			->where('dmr_result','成功')
            ->field([
                'dmr_time' => 'dmrTime',
                'dmr_money' => 'drivMoney' 
            ])
            ->select();
		echo json_encode($list);
		exit;
	}
	public function userBill()
	{
		$openid=Request::instance()-> post('openid');
		$list = DB::name('order_list')
					->alias('o')//给表起别名
            ->join('business_type b', 'b.bt_id = o.bt_id')//联表查询
            ->join('user u', 'u.user_id = o.user_id')//联表查询
            ->join('driver d', 'd.driv_id = o.driv_id')//联表查询
            ->join('recharge_pay_type r', 'r.rpt_id = o.rpt_id')//联表查询
            ->join('order_list_status ols', 'ols.ols_id = o.ols_id')//联表查询
            ->where('u.open_id',$openid)
			->where('ols.ols_id','=','5')
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
		echo json_encode($list);
		exit;
	}
} 
?>