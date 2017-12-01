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
define("APPID",'wx870f25b8a2a98f0b');
define("APPSECRET",'d51063fe3c3b3f30688c74f1f86ab768');
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

        file_put_contents('debug-1.txt', '进入消息回复:'.json_encode($postObj));
        $access_token=$this->getAccessToken();      //用封装好的内置方法获取access_token(有判断)

        $data=[
            'key'=>TULINGAPIKEY,            //图灵接口的key
            'info'=>$postObj->Content       //用户发送的消息
        ];

        $resMsg=curlHttp('http://www.tuling123.com/openapi/api',$data); //调用图灵接口回答的数据

        $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;//客服自动回复消息
        $msg=[
            "touser"=>$postObj->FromUserName,           //用户openid
            "msgtype"=>"text",                           //类型是文字
            "text"=> ["content"=>$resMsg->text]         //图灵回复的消息
        ];

        curlHttp($url,json_decode($msg));        //发送回微信小程序
    }

    /*小程序获取access_token*/
    public function getAccessToken()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode($this->get_php_file("access_token.php"));
        if ($data->expire_time < time()) {
            //  超时则获取新access_token，并做保存操作
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
            $res = json_decode(curlHttp($url,[]));  //转换成json格式
            $access_token = $res->access_token;     //获取其中的access_token
            if ($access_token) {
                //如果获取到access_token，做一个时间增加，并储存至文件
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $this->set_php_file("access_token.php", json_encode($data));
            }
        } else {
            $access_token = $data->access_token;        //如果没有超时，则调用原来的access_token
        }
        return $access_token;       //返回access_token

    }

    public function set_php_file($filename, $content) {
        $fp = fopen($filename, "w");                //用写入方式打开文件
        fwrite($fp, "<?php exit();?>" . $content);//写入标识加上数据
        fclose($fp);                                //关闭资源
    }

    public function get_php_file($filename) {
        return trim(substr(file_get_contents($filename), 15));//截取标识，获取标识后的数据
    }

}