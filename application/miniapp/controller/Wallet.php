<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class Wallet extends Controller {
	//司机余额显示
    public function driverWallet()
	{
		$openid=Request::instance()-> post('openid');
		//echo $openid;
		//exit;
		$list = DB::name('driver')
					->where('open_id',$openid)
					->find();
		echo json_encode($list);
		exit;
	}
	//用户余额显示
	public function userWallet()
	{
		$openid=Request::instance()-> post('openid');
		$list = DB::name('user')
					->where('open_id',$openid)
					->find();
		echo json_encode($list);
		exit;
	}
	//用户假装充值
	public function Recharge()
	{
		$money=Request::instance()-> post('money');
		$openid=Request::instance()-> post('openid');
		//查询现有的钱
		$list = DB::name('user')
					->where('open_id',$openid)
					->find();
		//金钱处理
		$list['user_money']+=floatval($money);
		$res=DB::name('user')
					->where('open_id',$openid)
					->update(['user_money' => $list['user_money']]);
		echo json_encode($res);
		exit;
	}
	//司机假装充值
	public function enchashment()
	{
		//取值
		$money=Request::instance()-> post('money');
		$openid=Request::instance()-> post('openid');
		//查询司机现有的钱
		$list = DB::name('driver')
					->where('open_id',$openid)
					->find();
		//扣款
		
		$list['driv_money']-=floatval($money);
		if($list['driv_money']<0)
		{
			echo 1;
			exit;
		}else
		{
			$res=DB::name('driver')
					->where('open_id',$openid)
					->update(['driv_money' => $list['driv_money']]);
			//传值
			echo json_encode($res);
			exit;
		}
		
	}
} 
?>

