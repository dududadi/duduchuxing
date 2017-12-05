<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Index extends Controller {
    public function index() {
    	$list = DB::name('news')
				->where('news_status','发布')
				->select();
		for($i=0;$i<count($list);$i++)
		{
			$arry=explode("/index.php",Request::instance()->root().$list[$i]['news_img']);
			$string=implode("/",$arry);
			$list[$i]['news_img']=$string;
		}
		$this->assign('list',$list);
        return $this -> fetch();
    }

    public function taxi() {
        return $this -> fetch();
    }

    public function privateTaxi() {
        return $this -> fetch('private_taxi');
    }

    public function aboutUs() {
        return $this -> fetch('about_us');
    }

    public function contactUs() {
        return $this -> fetch('contact_us');
    }
}
