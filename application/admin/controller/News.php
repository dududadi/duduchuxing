<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
	
class News extends Controller
{
	//信息推广页面
	public function spread()
	{
		return $this->fetch();
	}
	//新闻发布页面
	public function publish()
	{
		return $this->fetch();
	}
	//图片上传
	public function pushNews()
	{
		
		$files=$_FILES['file'];//类型
		$path='../public/static/img/hy';//路径
		$filename='news_'.time();//文件名
		$res=$this->saveUpload($files,$path,$filename);
		Session::set('news_url',$res);
	}
	//新闻上传
	public function subNews()
	{
		$news_url=Session::get('news_url');
		$newsTitle=trim(input('post.newsTitle',''));
		$datemin=trim(input('post.datemin',''));
		$newsType=trim(input('post.newsType',''));
		$newsContent=trim(input('post.newsContent',''));
		$data =[
			'news_id' => '',
			'news_tile' =>$newsTitle,
			'news_release_time' =>$datemin,
			'news_status' =>$newsType,
			'news_img' =>$news_url,
			'news_content' =>$newsContent
		]
		$res= DB::name('news')->insert($data);
	}
	//新闻编辑页面
	public function edit()
	{
		return $this->fetch();
	}
	private function saveUpload($files, $path, $filename) {

        if(mb_substr($path,mb_strlen($path)-1) !== '/') {
            $path .= '/';
        }
        if (!is_dir($path)) {
            mkdir($path);
        }
        if ($files['error'] === 0) {
            if (substr_count($files['type'], 'image') > 0) {
                $pos = strrpos($files['name'],'.'); // 找到后缀名前的.
                $name = $filename.substr($files['name'], $pos);
                $path .= $name;
                move_uploaded_file($files['tmp_name'], $path); // 将文件从临时文件夹存放到指定文件夹
                $res = ['success' => $path]; //返回文件路径
            } else {
                //不是图像
                $res = ['errorCode' =>'0'];
            }
        } else {
            if ($files['type'] == '') {
                //空文件
                $res = ['errorCode' =>'-1'];
            } else {
                //文件上传失败
                $res = ['errorCode' =>'-2'];
            }
        }
        return $res;
    }
	
}
 
?>