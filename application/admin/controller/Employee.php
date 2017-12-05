<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Response;
use think\Session;
use think\Request;

class Employee extends Controller{
    //构造函数
    public function _initialize() {
        if(!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }

    //员工列表
    public function lists() {
        date_default_timezone_set('PRC'); //设置时区
        $getPageSize = input('get.pageSize','5');
        $getDateMin = input('get.dateMin');
        $getDateMax = input('get.dateMax');
        $getSearch = input('get.keyword');
        $dateMin = isset($getDateMin)?date('Y-m-d',strtotime($getDateMin)):'1970-1-1';
        $dateMax = isset($getDateMax)?date('Y-m-d',strtotime($getDateMax)+86400):date('Y-m-d',time()+86400);
        if (isset($getSearch)) {
            $keywordArr = mb_split('@',$getSearch);
        }

        //拼接条件
        $whereDate = [];
        $whereKeyword_name = '1=1';
        $whereKeyword_nickname = '1=1';

        $whereDate[] = ['>=',$dateMin];
        $whereDate[] = ['<=',$dateMax];

        if (isset($keywordArr)) {
            foreach ($keywordArr as $item) {
                $whereKeyword_name .= " and emp_name like '%{$item}%'";
                $whereKeyword_nickname .= " and emp_nickname like '%{$item}%'";
            }
        }

        //查询员工列表数据
        $data = Db::name('employee')
            ->alias('t1')
            ->join('d_role t2','t1.role_id = t2.role_id')
            ->join('d_province t3','t3.prov_num = t1.prov_num')
            ->join('d_city t4','t4.city_num = t1.city_num')
            ->join('d_area t5','t5.area_num = t1.area_num')
            ->where(empty($whereDate)?[]:['emp_reg_time' => $whereDate])
            ->where('('.$whereKeyword_name.') or ('.$whereKeyword_nickname.')')
            ->order('t1.emp_id asc')
            ->field('t1.emp_id,t1.emp_name,t1.emp_status,t2.role_id,t2.role_name,t3.prov_name,t4.city_name,t5.area_name')
            ->paginate($getPageSize , false , ['type'=>'Hui']);
        $this->assign('list',$data); //绑定列表数据
        $this->assign('pageSize',$getPageSize); //绑定分页数据
        $this->assign('dateMin',$getDateMin); //绑定最小日期数据
        $this->assign('dateMax',$getDateMax); //绑定最大日期数据
        $this->assign('keyword',$getSearch); //绑定关键字数据
        return $this->fetch();

    }

    //查询员工详细信息
    public function details() {
        $hasEmpId = Request::instance()->has('id','get');
        if (!$hasEmpId) {
            return '<h1>请求出错！请联系管理员！</h1>';
        } else {
            $empId = Request::instance()->get('id');
            $empData = Db::name('employee')
            ->alias('t1')
            ->join('d_role t2','t1.role_id = t2.role_id')
            ->join('d_province t3','t3.prov_num = t1.prov_num')
            ->join('d_city t4','t4.city_num = t1.city_num')
            ->join('d_area t5','t5.area_num = t1.area_num')
            ->where('t1.emp_id',$empId)
            ->column('t1.emp_id,t1.emp_name,t1.emp_status,t1.emp_head_img,t1.emp_nickname,t1.emp_reg_time,t2.role_name,t3.prov_name,t4.city_name,t5.area_name');
            $data['data'] = json_encode($empData);
            $data['id'] = $empId;
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    //添加员工页面
    public function add(){
        if(!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
        $editId = input('get.id','');
        if (!empty($editId)) {
            $data = DB::name('employee')
            ->alias('t1')
            ->where('emp_id',$editId)
            ->field('emp_id,emp_name,emp_nickname,prov_num,city_num,area_num,role_id,emp_head_img')->find();
            $this->assign('data',json_encode($data));
        } else {
            $this->assign('data',json_encode(''));
        }
        return $this->fetch();
    }

    //添加或编辑员工操作
    public function addEmp() {
        //$nowRoleId = (string)Session::get('role_id');
        //if ($nowRoleId !== '1') return json('0');
        $id = input('get.id','');
        $name = input('post.adminName', '');
        $nickname = input('post.adminNickname', '');
        $psw = input('post.password', '');
        $psw2 = input('post.password2', '');
        $province = input('post.province', '');
        $city = input('post.city', '');
        $area = input('post.area', '');
        $roleId = input('post.adminRole', '');
        $hasHeadImg = empty($_FILES)?false:$_FILES['file-2']['error'] === 0;
        date_default_timezone_set('PRC'); //设置时区
        $timestamp = time();  //当前时间戳
        if ($name==='' || $nickname==='' || $psw==='' || $psw2==='' || $province==='' || $city==='' || $roleId==='') {
            return json('-1');
        } else {
            if ($hasHeadImg) {
                $path = $_SERVER['DOCUMENT_ROOT'].Request::instance()->root().'/static/img/update/emp_headImg'; //上传文件存储路径
                $savePath = $this->saveUpload($_FILES['file-2'], $path, $name.$timestamp); //将上传文件保存
            }

            if ($id) {
                $imgPath = Db::name('employee')->where('emp_id', $id)->value('emp_head_img');
                $res = Db::name('employee')
                ->where('emp_id', $id)
                ->update([
                    'emp_psw' => md5($psw),
                    'emp_name' => $name,
                    'emp_nickname' => $nickname,
                    'role_id' => $roleId,
                    'prov_num' => $province,
                    'city_num' => $city,
                    'area_num' => $area,
                    'emp_head_img' => $hasHeadImg?isset($savePath['errorCode'])?'defaultHead.jpg':'emp_headImg/'.$savePath['filename']:'defaultHead.jpg'
                ]);
                if ($hasHeadImg && $imgPath && $res && $imgPath!=='defaultHead.jpg') @unlink( $_SERVER['DOCUMENT_ROOT'].Request::instance()->root().'/static/img/update/'.$imgPath);
                if ($res === 1) {
                    return json('2');
                } else {
                    return json('0');
                }
            } else {
                $data = [
                    'emp_id' => '',
                    'emp_reg_time' => date('Y-m-d H:i:s', $timestamp),
                    'emp_psw' => md5($psw),
                    'emp_name' => $name,
                    'emp_nickname' => $nickname,
                    'role_id' => $roleId,
                    'prov_num' => $province,
                    'city_num' => $city,
                    'area_num' => $area,
                    'emp_status' => '使用',
                    'emp_head_img' => $hasHeadImg?isset($savePath['errorCode'])?'defaultHead.jpg':'emp_headImg/'.$savePath['filename']:'defaultHead.jpg'
                ];
                $res = Db::name('employee')->insert($data);
                if ($res === 1) {
                    return json('1');
                } else {
                    return json('0');
                }
            }
        }
    }

    //锁定
    public function lock() {
        $id = input('get.id','');
        $res = Db::name('employee')
        ->where('emp_id', $id)
        ->update([
            'emp_status' => '锁定'
        ]);
        return json($res);
    }

    //批量锁定
    public function lockAll() {
        $checkArr = input('post.checkArr','');
        $bool = [];
        $checkArr = json_decode($checkArr,true);
        foreach ($checkArr as $item) {
            $res = Db::name('employee')
                ->where('emp_id', $item)
                ->update([
                    'emp_status' => '锁定'
                ]);
            if($res !== 0) {
                $bool[] = $item;
            }
        }
        return json($bool);
    }

    //解锁
    public function unlock() {
        $id = input('get.id','');
        $res = Db::name('employee')
        ->where('emp_id', $id)
        ->update([
            'emp_status' => '使用'
        ]);
        return json($res);
    }

    //批量解锁
    public function unlockAll() {
        $checkArr = input('post.checkArr','');
        $bool = [];
        $checkArr = json_decode($checkArr,true);
        foreach ($checkArr as $item) {
            $res = Db::name('employee')
                ->where('emp_id', $item)
                ->update([
                    'emp_status' => '使用'
                ]);
            if($res !== 0) {
                $bool[] = $item;
            }
        }
        return json($bool);
    }

    //删除
    public function delete() {
        $id = input('get.id','');
        $res[] = Db::name('employee')->where('emp_id', $id)->delete();
        $res[] = Db::name('comment_dts')->where('emp_id', $id)->delete();
        $res[] = Db::name('chat_record_us')->where('emp_id', $id)->delete();
        $res[] = Db::name('chat_record_ds')->where('emp_id', $id)->delete();
        if (in_array(1,$res)) {
            return json(true);
        } else {
            return json(false);
        }
    }

    public function getSelectVal() {
        //获取角色下拉菜单
        $roleData = db('role')->column('role_id,role_name');
        $roleData = empty($roleData)?'0':$roleData;
        //获取省份下拉菜单
        $provinceData = db('province')->column('prov_num,prov_name');
        $provinceData = empty($provinceData)?'0':$provinceData;
        $data = ['role' => $roleData,'province' => $provinceData];
        return json($data);
    }

    //获取城市下拉菜单
    public function getCity() {
        $provNum = Request::instance()->post('provNum','');
        $cityData = db('city')->where('prov_num',$provNum)->column('city_num,city_name');
        $cityData = empty($cityData)?'0':$cityData;
        $data = ['city' => $cityData];
        return json($data);
    }

    //获取区/县下拉菜单
    public function getArea() {
        $cityNum = input('post.cityNum','');
        $areaData = db('area')->where('city_num',$cityNum)->column('area_num,area_name');
        $areaData = empty($areaData)?'0':$areaData;
        $data = ['area' => $areaData];
        return json($data);
    }

    //对上传文件存储
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
                $res = ['path' => $path, 'filename' => $name]; //返回文件路径
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