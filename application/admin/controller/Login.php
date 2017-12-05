<?php
namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类

class Login extends Controller {
    public function _initialize() {
        if (sessionAssist('isLogin')) {
            $this -> redirect('Index/index');
        } else if (cookieAssist('isLogin')) {
            //判断当前用户是否存在凭证，有则直接跳转主页
            $user=cookie('isLogin');

            //将当前用户ID存入session中
            Session::set('isLogin',$user);

            $this -> redirect('Index/index');
        }
    }

    public function index() {
        return $this -> fetch('index/login');
    }

    public function checkUser() {
        $captcha=trim(input('post.code','')); //获取页面传递的验证码

        if(captcha_check($captcha)){
            //判断表单是否有传值过来  没有就赋值为空
            $user = trim(input('post.user',''));//获取页面传递的ID
            $upas = trim(input('post.password',''));//获取页面传递的密码

            $res = Db::name('employee')
            -> where(['emp_id'=>$user,'emp_psw'=>md5($upas)])
            -> find();    //带着查询条件向数据库查询
            if(!empty($res)){
                if($res['emp_status']=='使用')
                {
                    //结果不为空  则session缓存更新
                    Session::set('isLogin',$res['emp_id']);
                    Session::set('emp_name',$res['emp_name']);
                    Session::set('role_id',$res['role_id']);
                    //跳转页面并友好提示
                    if(!empty(input('post.online')))
                    {
                        //结果不为空，则说明选择了七天登录，所以存下当前ID作为cookie凭证，并设置7天期限
                        cookie('isLogin', $res['emp_id'], 604800);
                    }
                    return 1;//登录成功
                }
                else
                {
                    //该用户被锁定
                    return 3;
                }
            }else{
                //登录失败,账号或密码输入错误
                return 2;
            }
        }else{
            //验证失败，请重新输入验证码
            //跳转页面并友好提示
            return 4;
        }
    }
}