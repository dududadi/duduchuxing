<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
	
class News extends Controller
{
	//信息推广
	public function spread()
	{
		return $this->fetch();
	}
	//信息发布
	public function publish()
	{
		return $this->fetch();
	}
	//新闻编辑
	public function edit()
	{
		return $this->fetch();
	}
}
?>