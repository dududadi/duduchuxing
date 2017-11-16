<?php
namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Db;                   //引用官方封装的数据库单例类
class Login extends Controller
{
    public function login() {
        return $this -> fetch('index/login');
    }



    public function checkUser()
    {

        $captcha=input('post.code',''); //获取页面传递的验证码

        if(captcha_check($captcha)){
            //验证成功
            $model = Db::name('employee');

            //判断表单是否有传值过来  没有就赋值为空
            $user = input('post.user','');//获取页面传递的ID
            $upas = input('post.password','');//获取页面传递的密码

            $res =$model->where(['emp_id'=>$user,'emp_psw'=>md5($upas)])->find();    //带着查询条件向数据库查询

            if(!empty($res))
            {
                //结果不为空  则session缓存更新
                Session::set('isLogin',$res['emp_id']);
                //跳转页面并友好提示
                $this ->success('登录成功','admin/index/index',3);
            }
            else
            {
                //跳转页面并友好提示
                $this ->error('登录失败,账号或密码输入错误','admin/login/login',3);
            }

        }else{
            //验证失败
            //跳转页面并友好提示
            $this ->error('验证失败，请重新输入验证码','admin/login/login',3);
        };


    }
}