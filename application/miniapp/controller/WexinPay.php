<?php
namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Paginator;
use think\Request;
use think\Session;
class Wexinpay extends Controller
{
	public function pay(){
	    $fee = 0.01;//举例充值0.01
	    $appid =        'wxdbf8a607a8dcdfa4';//如果是公众号 就是公众号的appid
	    $body =         '卡文网络';
	    $mch_id =       '1331063701';
	    $nonce_str =    $this->nonce_str();//随机字符串
	    $openid =       input('get.openid');
	    $out_trade_no = $this->order_number();//商户订单号
	    $spbill_create_ip = '47.100.0.162';
	    $total_fee =    $fee*100;//因为充值金额最小是1 而且单位为分 如果是充值1元所以这里需要*100
	    $trade_type = 'JSAPI';//交易类型 默认
	
	    //这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
	    $post['appid'] = $appid;
	    $post['body'] = $body;
	    $post['mch_id'] = $mch_id;
	    $post['nonce_str'] = $nonce_str;//随机字符串
	    $post['openid'] = $openid;
	    $post['out_trade_no'] = $out_trade_no;
	    $post['spbill_create_ip'] = $spbill_create_ip;//终端的ip
	    $post['total_fee'] = $total_fee;//总金额 最低为一块钱 必须是整数
	    $post['trade_type'] = $trade_type;
	    $sign = $this->sign($post);//签名
	    $post_xml = '<xml>
	           <appid>'.$appid.'</appid>
	           <body>'.$body.'</body>
	           <mch_id>'.$mch_id.'</mch_id>
	           <nonce_str>'.$nonce_str.'</nonce_str>
	           <openid>'.$openid.'</openid>
	           <out_trade_no>'.$out_trade_no.'</out_trade_no>
	           <spbill_create_ip>'.$spbill_create_ip.'</spbill_create_ip>
	           <total_fee>'.$total_fee.'</total_fee>
	           <trade_type>'.$trade_type.'</trade_type>
	           <sign>'.$sign.'</sign>
	        </xml> ';
	    //统一接口prepay_id
	    $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
	    $xml = $this->http_request($url,$post_xml);
	    $array = $this->xml($xml);//全要大写
	    if($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS'){
	        $time = time();
	        $tmp='';//临时数组用于签名
	        $tmp['appId'] = $appid;
	        $tmp['nonceStr'] = $nonce_str;
	        $tmp['package'] = 'prepay_id='.$array['PREPAY_ID'];
	        $tmp['signType'] = 'MD5';
	        $tmp['timeStamp'] = "$time";
	
	        $data['state'] = 1;
	        $data['timeStamp'] = "$time";//时间戳
	        $data['nonceStr'] = $nonce_str;//随机字符串
	        $data['signType'] = 'MD5';//签名算法，暂支持 MD5
	        $data['package'] = 'prepay_id='.$array['PREPAY_ID'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
	        $data['paySign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;
	        $data['out_trade_no'] = $out_trade_no;
	
	    }else{
	        $data['state'] = 0;
	        $data['text'] = "错误";
	        $data['RETURN_CODE'] = $array['RETURN_CODE'];
	        $data['RETURN_MSG'] = $array['RETURN_MSG'];
	    }
	    echo json_encode($data);
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
	
	
	
	//签名 $data要先排好顺序
	private function sign($data){
	    $stringA = '';
	    foreach ($data as $key=>$value){
	        if(!$value) continue;
	        if($stringA) $stringA .= '&'.$key."=".$value;
	        else $stringA = $key."=".$value;
	    }
	    $wx_key ='af322231e835171608478e16b04889d9';//申请支付后有给予一个商户账号和密码，登陆后自己设置key
	    $stringSignTemp = $stringA.'&key='.$wx_key;//申请支付后有给予一个商户账号和密码，登陆后自己设置key    return strtoupper(md5($stringSignTemp));}
	
	//curl请求啊
	function http_request($url,$data = null,$headers=array())
	{
	    $curl = curl_init();
	    if( count($headers) >= 1 ){
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    }
	    curl_setopt($curl, CURLOPT_URL, $url);
	
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	
	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
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
?>