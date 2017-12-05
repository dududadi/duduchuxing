<?php
/**
 * Created by PhpStorm.
 * User: huangjianwu
 * Date: 2017/12/5
 * Time: 17:19
 */
namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Db;
use think\Session;
class Chat extends Controller {
    public function _initialize() {
        if (sessionAssist('isLogin')) {
            echo "hello 1111";exit;
            $this -> redirect('http://www.hjw123.xin:55151/');
        } else if (cookieAssist('isLogin')) {
            //判断当前用户是否存在凭证，有则直接跳转主页
            $user=cookie('isLogin');

            //将当前用户ID存入session中
            Session::set('isLogin',$user);
            echo "hello word";exit;
            $this -> redirect('http://www.hjw123.xin:55151/');
        }
    }
}