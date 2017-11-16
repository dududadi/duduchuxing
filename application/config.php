<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// /初始化指向根目录的变量
use think\Request;

$request = Request::instance();
$base    = $request->root();
$root    = strpos($base, '.') ? ltrim(dirname($base), DS) : $base;

if ('' != $root) {
    $root = '/' . ltrim($root, '/');
}

return [
    // +----------------------------------------------------------------------
    // | 个人应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'               => true,
    // 应用Trace
    'app_trace'               => true,
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__IMG__'           => $root.'/static/img',
        '__FONT__'          => $root.'/static/font',
        '__BOOTSTRAP__'    => $root.'/static/bootstrap',
        '__FULLPAGE__'     => $root.'/static/fullPage',
        '__HUI__'           => $root.'/static/hui'
    ],
    //验证码配置
    'captcha'                 => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)
        'fontSize' => 25,
        // 是否画混淆曲线
        'useCurve' => true,
         // 验证码图片高度
        'imageH'   => 30,
        // 验证码图片宽度
        'imageW'   => 100,
        // 验证码位数
        'length'   => 5,
        // 验证成功后是否重置
        'reset'    => true
    ],

];
