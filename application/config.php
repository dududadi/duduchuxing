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
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => true,
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__IMG__'       => $root.'/static/img',
        '__FONT__'      => $root.'/static/font',
        '__BOOTSTRAP__' => $root.'/static/bootstrap'
    ],

];
