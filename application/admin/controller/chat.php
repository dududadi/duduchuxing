<?php
/**
 * Created by PhpStorm.
 * User: huangjianwu
 * Date: 2017/12/5
 * Time: 17:19
 */
namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
class Chat extends Controller {
    public function _initialize() {
        if (sessionAssist('isLogin')) {
            $this -> redirect('Chat/chat');
        } else if (cookieAssist('isLogin')) {
            //判断当前用户是否存在凭证，有则直接跳转主页
            $user=cookie('isLogin');

            //将当前用户ID存入session中
            Session::set('isLogin',$user);

            $this -> redirect('Chat/chat');
        }

    }
}