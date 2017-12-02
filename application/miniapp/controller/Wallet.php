<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;

class Wallet extends Controller {
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
	public function userWallet()
	{
		$openid=Request::instance()-> post('openid');
		$list = DB::name('user')
					->where('open_id',$openid)
					->find();
		echo json_encode($list);
		exit;
	}
} 
?>

