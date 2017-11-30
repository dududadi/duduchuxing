<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/11/30
 * Time: 10:27
 */

namespace app\miniapp\controller;

use think\Controller;

define("TOKEN",'maygodblessus');
define("USERAPPID",'wx870f25b8a2a98f0b');
define("USERAPPSECRET",'d51063fe3c3b3f30688c74f1f86ab768');
define('TULINGAPIKEY','186d105734dd42dd9a8e3f4607a873d4');
class Conversation extends Controller
{

    /*进行微信接入*/
    public function wechatApi(){
        /*获取微信发送过来的校验随机字符串*/
        $echoStr = isset($_GET['echostr']) ? $_GET['echostr'] : '';

        /*判断微信是否有发送过来，如果有发送过来
        表示它对我们的服务器还没有信任，需要我们先接入配置
        如果没发送过来了
        那么表示接入已经成功了，微信就会放心的把粉丝的消息发送过来*/

        if(empty($echoStr)){
            $this->response();
        } else{
            $this->valid();
        }
    }

    /*做验证操作*/
    public function valid(){
        /*获取微信发送过来的 echostr，随机字符串*/
        $echoStr = $_GET['echostr'];

        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }else{
        }
    }

    /*验证算法*/
    public function checkSignature(){


        if(!defined("TOKEN")){
            throw new Exception("TOKEN is not defined!");
        }

        /*获取微信发送过来的校验结果，也就是微信官方已经做好的饭团*/
        $signature = $_GET['signature'];

        /*获取微信发送过来的材料之一，时间戳*/
        $timestamp = $_GET['timestamp'];

        /*获取微信发送过来的材料之一，随机数*/
        $nonce = $_GET['nonce'];

        /*双方都定义好的暗号*/
        $token = TOKEN;


        //字典排序
        $tmpArr = array($token,$timestamp,$nonce);

        sort($tmpArr,SORT_STRING);

        /*将新排序后的数组再分割成字符串*/
        $tmpStr = implode($tmpArr);

        /*sha1 加密*/
        $tmpStr = sha1($tmpStr);


        if($tmpStr == $signature){
            return true;
        }
        else{
            return false;
        }
    }

    /*验证成功后，捕捉粉丝的消息，并且回复*/
    public function response(){
        /*微信是以XML格式发送给我们所以我们要以 php 获取XML数据流格式的方式去获取*/
        $fensMsg = $GLOBALS['HTTP_RAW_POST_DATA'];
        /*接受到的粉丝的消息数据是以XML格式获取的，
        由于PHP中，对数组的操作最便捷，所以php中很习惯的将数据转换成数组来处理*/

        libxml_disable_entity_loader(ture);
        $postObj = simplexml_load_string($fensMsg,'SimpleXMLElement',LIBXML_NOCDATA);

        //$arr   =  json_decode(json_encode($xml),TRUE);	//将XML转换后的字符串，变成标准的json格式字符串，再转成数组

        $data=[
            'key'=>TULINGAPIKEY,
            'info'=>$postObj->Content
        ];

        $resMsg=curlHttp('http://www.tuling123.com/openapi/api',$data);

        echo '<xml>
					<ToUserName><![CDATA['.$postObj->FromUserName.']]></ToUserName>
					<FromUserName><![CDATA['.$postObj->ToUserName.']]></FromUserName>
					<CreateTime>'.time().'</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA['.$resMsg->text.']]></Content>
					</xml>';


    }

}