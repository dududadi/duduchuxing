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
		Session::set('packeg_id',$packeg_id['prepay_id']);
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
	public function packeg_id($openid)
	{
		$order_number=$this->order_number($openid);
		$nonceStr=$this->nonceStr();
		$KnonceStr=Session::get('KnonceStr');
		$url='https://api.mch.weixin.qq.com/pay/unifiedorder';
		$data=[
	    	'appid' =>'wxdbf8a607a8dcdfa4',
	    	'mch_id' =>1331063701,
	    	'timeStamp' =>time(),
	    	'nonceStr' =>$KnonceStr,
	    	'body'	=> '嘟嘟出行-充值',
	    	'out_trade_no' =>$order_number,  //订单号
	    	'sign' =>$this->packeg_sign($order_number),
	    	'total_fee' => 1,
	    	'spbill_create_ip'=> '47.100.0.162',
	    	'notify_url' =>'https://www.forhyj.cn/miniapp/WexinPay/Pay/notify_url',
	    	'trade_type'=>'JSAPI'];
		ksort($data);
	    //进行拼接数据
	    $abc_xml = "<xml>";
	    foreach ($data as $key => $val) {
	        if (is_numeric($val)) {
	            $abc_xml .= "<" . $key . ">" . $val . "</" . $key . ">";
	        } else {
	            $abc_xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
	        }
	    }
	    $abc_xml .= "</xml>";
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    	$info = http_request_curl($url, $abc_xml);
		return $info;
	}
	//生成packeg——sign
	public function packeg_sign($order_number)
	{
		$KnonceStr=$this->nonce_str();
		Session::set('KnonceStr',$KnonceStr);
		$key='af322231e835171608478e16b04889d9';
		 $data=[
	    	"appid" =>'wxdbf8a607a8dcdfa4',
	    	"mch_id" =>1331063701,
	    	"timeStamp" =>time(),
	    	"nonceStr" =>$KnonceStr,
	    	'body'	=> '嘟嘟出行-充值',
	    	'out_trade_no' =>$order_number,  //订单号
	    	'total_fee' => 1,
	    	'spbill_create_ip'=> '47.100.0.162',
	    	'notify_url' =>"https://www.forhyj.cn/miniapp/WexinPay/Pay/notify_url",
	    	'trade_type'=>'JSAPI'
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
	//生成发送sign
	public function sign($nonceStr)
	{
		$key='af322231e835171608478e16b04889d9';
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
	public function notify_url()
	{
		echo "我求求你别报错了";
		exit;
	}

	//url请求
	function http_request_curl($url, $rawData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: text'
            )
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
