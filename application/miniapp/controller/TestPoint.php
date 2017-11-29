<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class TestPoint extends Controller{
    public function index()
    {
        $pointArr = Request::instance()->post('pointArr');
        file_put_contents('./static/img/ljf/point.php',$pointArr,FILE_APPEND);
    }

}