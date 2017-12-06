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
			->join('d_user u','u.user_id = dmr.user_id')		//联表查询 司机钱包记录表
            ->where('d.open_id',$openid)			//将微信开放ID作为凭证向数据库查询信息
			->where('dmr_result','成功')			//获取结果为成功的钱包记录
            ->field([
                'dmr_time' => 'dmrTime',			//结款时间
                'dmr_money' => 'drivMoney',	//金额
                'umr_time' =>'dmrTime',			//支付时间
                'user_name'	=>'userName'
            ])
            ->select();
		echo json_encode($list);
		exit;
	}
	public function userBill()
	{
		$openid=Request::instance()-> post('openid');
		$list = DB::name('user')
					->alias('u')//给表起别名
			->join('driv_money_record dmr','dmr.user_id = d.user_id')		//联表查询 司机钱包记录表
            ->join('d_driver d','d.driv_id = dmr.driv_id')
            ->where('u.open_id',$openid)			//将微信开放ID作为凭证向数据库查询信息
			->where('dmr_result','成功')			//获取结果为成功的钱包记录
            ->field([
                'dmr_time' => 'dmrTime',			//结款时间
                'dmr_money' => 'drivMoney',	//金额
                'umr_time' =>'dmrTime',
                'driv_name'	=>'drivName'		//支付时间
            ])
			->select();
		echo json_encode($list);
		exit;
	}
} 
?>