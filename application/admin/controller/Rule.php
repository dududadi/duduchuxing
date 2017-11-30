<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类
use think\Request;               //引用变量获取使用

class Rule extends Controller{
	//构造函数
	public function _initialize(){
		if(!sessionAssist('isLogin')){
			$this -> redirect('Login/index');
		}
	}

	//进入快车规则管理页面
	public function car(){
		return $this -> fetch();
	}
	//进入出租车规则管理页面
	public function taxi(){
		return $this -> fetch();
	}
	//显示快车规则
	public function showCar(){
		//获取规则
		$carRule = Db::name('rule')
			->where('bt_id',1)
			->select();
		echo json_encode($carRule);
		exit;
	}
	//修改快车规则
	public function saveCarRule(){
		$carRule = Request::instance()-> post('carRule/a');
		
		for($i=0;$i<count($carRule);$i++){

		    Db::name('rule')
				-> where('rule_id',$carRule[$i]['rule_id'])
				-> update(['rl_price'=>$carRule[$i]['rl_price']]);
			
		}
		
		exit;
		
	}
	//显示出租车规则
	public function showTaxi(){
		$taxiRule = Db::name('rule')
			->where('bt_id',2)
			->select();
		echo json_encode($taxiRule);
		exit;
	}
	//修改出租车规则
	public function saveTaxiRule(){
		$taxiRule = Request::instance()-> post('taxiRule/a');
		
		for($i=0;$i<count($taxiRule);$i++){

		    Db::name('rule')
				-> where('rule_id',$taxiRule[$i]['rule_id'])
				-> update(['rl_price'=>$taxiRule[$i]['rl_price']]);
			
		}
		
		exit;
		
	}
}


?>