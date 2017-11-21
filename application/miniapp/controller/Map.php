<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
class Map extends Controller
{
	public function getPath()
	{
		dump(input("post.latitude"));
	}
	public function calculate()
	{
		
	}
	public function getDistance($lat1, $lng1, $lat2, $lng2){   
		$radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
	    $radLat2=deg2rad($lat2);
	    $radLng1=deg2rad($lng1);
	    $radLng2=deg2rad($lng2);
	    $a=$radLat1-$radLat2;
	    $b=$radLng1-$radLng2;
	    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;
	    return $s;   
	}
}   
?>