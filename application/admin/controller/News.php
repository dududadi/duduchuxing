<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
	
class News extends Controller
{
	public function spread()
	{
		return $this->fetch();
	}
}
?>