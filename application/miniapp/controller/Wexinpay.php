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
		//获取包id
		$json_packeg=$this->packeg_id();
		$appId='wxdbf8a607a8dcdfa4';
		$openid='owqCguL4_azImvEDAzM-KnNv6MUE';
		$nonceStr=$this->nonce_str($openid);
		//将xml转json
		$array_packeg=json_decode($json_packeg, true);
		//获取packegid
		$packeg_id=$array_packeg['PREPAY_ID'];
		$timeStamp=(string)time();
		$sign=$this->sign($appId,$nonceStr,$timeStamp,$packeg_id);
		//var_dump($sign);
		$data=[
			'appId' => $appId,						
	        'nonceStr' => $nonceStr,				//随机数
	        'package' => 'prepay_id='.$packeg_id,	//包id
	        'signType' => 'MD5',					//md5
	        'timeStamp' => $timeStamp,				//时间戳
	        'paySign' =>$sign						//签名
		];
		$data=json_encode($data);
		//var_dump($data);
		//exit;
		echo $data;
		exit;
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
		//订单号
		$order_number=$this->order_number($openid);
		//随机数
		$KnonceStr=$this->nonce_str();
		//url
		$url='https://api.mch.weixin.qq.com/pay/unifiedorder';
		//小程序appid
		$appid='wxdbf8a607a8dcdfa4';
		//商户号
		$mch_id='1331063701';
		//类型
		$body='嘟嘟出行-充值';
		//服务器地址
		$spbill_create_ip='47.100.0.162';	
		//回调地址		
		$notify_url='https://www.forhyj.cn/miniapp/WexinPay/Pay/notify_url';
		//传值格式（固定）
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
		//$xmlNode = simplexml_load_file($info);
		//$arrayData = xmlToArray($xmlNode);
		//$json = json_encode($arrayData);
		$array = $this->xml($info);
		$sign1 =json_encode($array);
		return $sign1;
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
	    //签名步骤二：在string后加入KEY
	    $string = $buff . "key=" . $key;
	    //签名步骤三：MD5加密
	    $string = md5($string);
	    //签名步骤四：所有字符转为大写
	    $sign = strtoupper($string);
		//echo '生成的签名---'.$sign;
		return $sign;
	}
	//生成发送签名
	public function sign($appId,$nonceStr,$timeStamp,$packeg_id)
	{
		$key='shadowhung1208kawenwangluosj1238';
		$data=[
			'appId' => $appId,
	        'nonceStr' => $nonceStr,
	        'package' => 'prepay_id=' . $packeg_id,
	        'signType' => 'MD5',
	        'timeStamp' => $timeStamp
		];
		ksort($data);
	    $buff = "";
	    foreach ($data as $k => $v) {
	        if ($k != "sign" && $v != "" && !is_array($v)) {
	            $buff .= $k . "=" . $v . "&";
	        }
	    }
	    //签名步骤二：在string后加入KEY
	    $string = $buff . "&key=" . $key;
	    //签名步骤三：MD5加密
	    $string = md5($string);
	    //签名步骤四：所有字符转为大写
	    $sign = strtoupper($string);
		return $sign;
	}
	//异步
	

	public function notify_url()
	{
		/*
     * 给微信发送确认订单金额和签名正确，SUCCESS信息 -xzz0521
     */
		//获取xml信息
        $postStr = post_data();
		$postXml=xml($postStr);
		//转成json
		$postXml =json_encode($postXml);
		//获取返回
        if($postXml['return_code']=='SUCCESS'){
            /*
            * 判断是否success
            */
            $this->return_success();
        }else{
            echo '微信支付失败';
        }
        exit;
	}
	//接收异步回调的方法
	function post_data(){
		$receipt = $_REQUEST;
		if($receipt==null){
			//接收xml数据
			$receipt = file_get_contents("php://input");
			if($receipt == null){
				//接收xml数据
				$receipt = $GLOBALS['HTTP_RAW_POST_DATA'];
			}
		}
		return $receipt;
	}
	/*
     * 给微信发送确认订单金额和签名正确，SUCCESS信息 -xzz0521
     */
    private function return_success(){
        $return['return_code'] = 'SUCCESS';
        $return['return_msg'] = 'OK';
        $xml_post = '<xml>
                    <return_code>'.$return['return_code'].'</return_code>
                    <return_msg>'.$return['return_msg'].'</return_msg>
                    </xml>';
        echo $xml_post;exit;
    }
	
	//获取xml
	private function xml($xml){
	    $p = xml_parser_create();
	    xml_parse_into_struct($p, $xml, $vals, $index);
	    xml_parser_free($p);
	    $data = "";
	    foreach ($index as $key=>$value) {
	        if($key == 'xml' || $key == 'XML') continue;
	        $tag = $vals[$value[0]]['tag'];
	        $value = $vals[$value[0]]['value'];
	        $data[$tag] = $value;
	    }
	    return $data;
	}
}
