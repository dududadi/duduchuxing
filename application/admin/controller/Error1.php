<?php
namespace app\admin\controller;

use think\Controller; 

//错误控制器访问
class Error extends Controller {
    //默认访问
    public function index() {
        $this -> redirect('Index/index');
    }

    //错误方法访问
    public function _empty(){ 
        $this -> redirect('Index/index');
    } 
}
