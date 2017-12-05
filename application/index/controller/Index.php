<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Index extends Controller {

    //首页渲染
    public function index() {
    	$list = DB::name('news')
				->where('news_status','发布')     //查询已发布新闻
				->select();
		for($i=0;$i<count($list);$i++)
		{
			$arry=explode("../public/static/img/",$list[$i]['news_img']);  //字符串处理   将'/index.php'删除
			$string=implode("/",$arry);                                                       //重组为字符串
			$list[$i]['news_img']=$string;                                           //重新赋值
		}
		$this->assign('list',$list);                                                            //再向页面传值
        return $this -> fetch();
    }

    //出租车页面渲染
    public function taxi() {
        return $this -> fetch();
    }

    //快车页面渲染
    public function privateTaxi() {
        return $this -> fetch('private_taxi');
    }

    //关于我们页面渲染
    public function aboutUs() {
        return $this -> fetch('about_us');
    }

    //联系我们页面渲染
    public function contactUs() {
        return $this -> fetch('contact_us');
    }
}
