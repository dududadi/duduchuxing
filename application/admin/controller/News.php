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
	//新闻图片上传
	public function pushNews()
	{
		
		$files=$_FILES['file'];//类型
		$path='../public/static/img/update';//路径
		$filename='news_'.time();//文件名
		$res=$this->saveUpload($files,$path,$filename);
		Session::set('newsUrl',$res);
	}
	//新闻上传
	public function subNews()
	{
		$newsUrl=Session::get('newsUrl');
		$newsTitle=trim(input('post.newsTitle',''));
		$datemin=trim(input('post.datemin',''));
		$newsType=trim(input('post.newsType',''));
		$newsContent=trim(input('post.newsContent',''));
		$data =[
			'news_id' => '',
			'news_title' =>$newsTitle,
			'news_release_time' =>$datemin,
			'news_status' =>$newsType,
			'news_img' =>$newsUrl['success'],
			'news_content' =>$newsContent
		];
		/*return $data;
		exit;*/
		$res= DB::name('news')->insert($data);
		echo $res;
	}
	//新闻编辑数据显示
	public function edit()
	{
		
 		if(input('?get.news')!=null)
		{
			$news=input('get.news');
			if(input('startTime')==null )
			{
				$startTime=('1970-1-1');
			}
			else
			{
				$startTime=input('get.starTime');
			}
			if(empty(input('get.endTime')))
			{
				$endTime=date($format);
			}
			else
			{
				$endTime=input('get.endTime');
			}
			$list = DB::name('news')
					->where('news_release_time','>',$startTime)
					->where('news_release_time','<',$endTime)
					->where('news_content|news_title','like','%'.$news.'%')
					-> paginate(6 , false , [
                    'type' => 'Hui',
                    'query' => [
                            'details'=>$details,
                            'startTime'=>$startTime,
                            'endTime'=>$endTime
                        ]
                    ]
                );	
		}else
		{
			$list = DB::name('news')
					->paginate(6,false,['type' => 'Hui']);
		}
		$this->assign('list',$list);
        return $this->fetch();//渲染出司机列表的页面
	}
	//新闻添加,就是进行新闻跳转
	public function addNews()
	{
		$this->publish();
	}
	//新闻改变状态
	public function ChangeState()
	{
		$id=trim(input('post.news_id'));
		$state=trim(input('post.news_status'));
		if($state=='未发布')
		{
			$res=DB::name('news')
			->where('news_id','=',$id)
			->setField('news_status','发布');
		}else
		{
			$res=DB::name('news')
			->where('news_id','=',$id)
			->setField('news_status','未发布');	
		}
		return $res;
	}
	//删除新闻
	public function delateNews()
	{
		$id=input('post.news_id');
		$res=DB::name('news')
			->where('news_id','=',$id)
			->delete();
		return $res;
	}
	
	//将图片保存到文件夹的方法
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