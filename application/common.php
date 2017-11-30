<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//判断session是否存在
function sessionAssist($name) {
    return session('?'. $name);
}

//判断cookie是否存在
function cookieAssist($name) {
    return cookie('?'. $name);
}

//curl发送请求
function curlHttp($url, $data) {
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_POST, true);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

    $res = curl_exec($curl);

    curl_close($curl);

    return $res;
}

//根据两个点的经纬度计算距离
function calculateDistance($lng1, $lat1, $lng2, $lat2) {
    // 将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
}



