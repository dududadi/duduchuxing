<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;
class Wexinpay extends Controller
{
	public function pay() {
		$openid=input('get.openid');
		$packeg_id=$this->packeg_id($openid);
		$appId='wxdbf8a607a8dcdfa4';
		$nonceStr=$this->nonce_str($openid);
		$data=[
			'appId' => $appId,
	        'nonceStr' => $nonceStr,
	        'package' => 'prepay_id='.$packeg_id['prepay_id'],
	        'signType' => 'MD5',
	        'timeStamp' => time(),
	        'paySign' =>$this->sign()
		];
		return $data;
	}
	//随机32位字符串
	private function nonce_str(){
	    $result = '';
	    $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
	    for ($i=0;$i<32;$i++){
	        $result .= $str[rand(0,48)];
	    }
	    return $result;
	}
	//生成订单号
	private function order_number($openid){
    //date('Ymd',time()).time().rand(10,99);//18位
    return md5($openid.time().rand(10,99));//32位
	}
	//生成packeg_id
	public function packeg_id()
	{
		$openid='owqCguL4_azImvEDAzM-KnNv6MUE';
		$order_number=$this->order_number($openid);
		//$order_number="20170806125346";
		$KnonceStr=$this->nonce_str();
		//$KnonceStr="5K8264ILTKCH16CQ2502SI8ZNMTM67VS";
		$url='https://api.mch.weixin.qq.com/pay/unifiedorder';
		//$sign="455225859B8BB8E2D73FE7627E198441";
		$appid='wxdbf8a607a8dcdfa4';
		$mch_id='1331063701';
		$body='嘟嘟出行-充值';
		$spbill_create_ip='47.100.0.162';
		$notify_url='https://www.forhyj.cn/miniapp/WexinPay/Pay/notify_url';
		$trade_type='JSAPI';
		$total_fee=1;
		$sign=$this->packeg_sign($order_number,$KnonceStr,$appid,$mch_id,$body,$spbill_create_ip,$notify_url,$trade_type,$total_fee,$openid); 
		$test ="<xml>
    <appid>".$appid."</appid>
    <body>".$body."</body>
    <mch_id>".$mch_id."</mch_id>
    <nonce_str>".$KnonceStr."</nonce_str>
    <notify_url>".$notify_url."</notify_url>
    <openid>".$openid."</openid>
    <out_trade_no>".$order_number."</out_trade_no>
    <spbill_create_ip>".$spbill_create_ip."</spbill_create_ip>
    <total_fee>1</total_fee>
    <trade_type>JSAPI</trade_type>
    <sign>".$sign."</sign>
 </xml> ";
		$url ='https://api.mch.weixin.qq.com/pay/unifiedorder';
		
    	$info =curlHttp($url, $test);
		var_dump($info);
		//var_dump($info);
		//exit;
		return $info;
	}
	//生成packeg——sign
	public function packeg_sign($order_number,$KnonceStr,$appid,$mch_id,$body,$spbill_create_ip,$notify_url,$trade_type,$total_fee,$openid)
	{
		$key='shadowhung1208kawenwangluosj1238';
		$data=[
	    	"appid" =>$appid,
	    	"mch_id" =>$mch_id,
	    	"nonce_str" =>$KnonceStr,
	    	'body'	=> $body,
	    	'out_trade_no' =>$order_number,  //订单号
	    	'total_fee' => $total_fee,
	    	'spbill_create_ip'=> $spbill_create_ip,
	    	'notify_url' =>$notify_url,
	    	'trade_type'=>$trade_type,
	    	'openid'=>$openid
	    ];
		
		ksort($data);
	    $buff = "";
	    foreach ($data as $k => $v) {
	        if ($k!= "sign"&&$v!= ""&&!is_array($v)) {
	            $buff.= $k."=".$v."&";
	        }
	    }
	    //$buff = trim($buff, "&");
	    //签名步骤二：在string后加入KEY
	    $string = $buff . "key=" . $key;
	    //签名步骤三：MD5加密
	    //echo 'before---'.$string;
	    //echo $string;
	    $string = md5($string);
	    //签名步骤四：所有字符转为大写
	    $sign = strtoupper($string);
		//echo '生成的签名---'.$sign;
		return $sign;
	}
	//生成发送sign
	public function sign($nonceStr)
	{
		$key='shadowhung1208kawenwangluosj1238';
		$packeg_id=Session::get('packeg_id');
		$data=[
			'appId' => 'wxdbf8a607a8dcdfa4',
	        'nonceStr' => $nonceStr,
	        'package' => 'prepay_id=' . $packeg_id,
	        'signType' => 'MD5',
	        'timeStamp' => time()
		];
		ksort($data);
	    $buff = "";
	    foreach ($data as $k => $v) {
	        if ($k != "sign" && $v != "" && !is_array($v)) {
	            $buff .= $k . "=" . $v . "&";
	        }
	    }
	    $buff = trim($buff, "&");
	    //签名步骤二：在string后加入KEY
	    $string = $buff . "&key=" . $key;
	    //签名步骤三：MD5加密
	    $string = md5($string);
	    //签名步骤四：所有字符转为大写
	    $sign = strtoupper($string);
		return $sign;
	}
	//统一生成签名
	

	public function notify_url()
	{
		echo "我求求你别报错了";
		exit;
	}
}
