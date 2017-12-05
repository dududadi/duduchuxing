<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Request;
use think\Db;
class Map extends Controller
{
	//计算两点之间距离
	public function getDistance($lat1, $lng1, $lat2, $lng2){   
		$radLat1=deg2rad($lat1);	//deg2rad()函数将角度转换为弧度
	    $radLat2=deg2rad($lat2);
	    $radLng1=deg2rad($lng1);
	    $radLng2=deg2rad($lng2);
	    $a=$radLat1-$radLat2;		//计算弧度差，两个点之间的距离，需要考虑地球弧度
	    $b=$radLng1-$radLng2;		//计算弧度差，两个点之间的距离，需要考虑地球弧度
	    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;//计算公式
	    return $s;   
	}

	//获取当前位置
	public function wxaddress(){
		$fromlat = $_GET['fromlat'];//接收起点纬度
		$fromlng = $_GET['fromlng'];//接收起点经度
		$tolat = $_GET['tolat'];//接收终点纬度
		$tolng = $_GET['tolng'];//接收终点经度
		$url = "http://apis.map.qq.com/ws/direction/v1/driving/?from=39.915285,116.403857&to=39.915285,116.803857&waypoints=39.111,116.112;39.112,116.113&output=json&callback=cb&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77";//接口地址
		$datajson = $this -> curlHttp($url,"");//发送请求并接收结果
		$obj = json_decode($datajson,true);//将返回的json转换成php能操作的对象；
		if ( $obj && $obj['status'] ==0 ) {
			apiResponse( "success" , "返回成功！" , $obj['result']['routes'][0] );//返回给小程序端。
		}else{
			apiResponse( "error" , "返回错误！" );
		}
	}
}   
?>