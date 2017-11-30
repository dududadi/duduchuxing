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
		$openid=input("post.openid");
		$list = DB::name('driver')
					->where('open_id','=',$openid)
					->find();
		echo $list;
		exit;
	}
	public function user_wallet()
	{
		$openid=input("post.openid");
	}
} 


