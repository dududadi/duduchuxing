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
			->join('driv_money_record dmr','dmr.driv_id = d.driv_id')		//联表查询 司机钱包记录表
            ->where('d.open_id',$openid)			//将微信开放ID作为凭证向数据库查询信息
			->where('dmr_result','成功')			//获取结果为成功的钱包记录
            ->field([
                'dmr_time' => 'dmrTime',			//结款时间
                'dmr_money' => 'drivMoney' 		//金额
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
            ->where('u.open_id',$openid)			//将微信开放ID作为凭证向数据库查询信息
			->where('ols.ols_id','=','5')			//将订单状态为待支付查询出来
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
            ])
            ->select();
		echo json_encode($list);
		exit;
	}
} 
?>