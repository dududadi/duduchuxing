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
        $pageSize = input('get.pageSize','5');
        $dateMin = input('get.dateMin','');
        $dateMax = input('get.dateMax','');
        if (input('get.keyword','') !== '') {
            $keywordArr = mb_split('@',input('get.keyword'));
            foreach ($keywordArr as $item) {
                $condition[] = ['emp_nickname','like',"%{$item}%"];
                $condition[] = ['emp_name','like',"%{$item}%"];
            }
        }
        //查询员工列表数据
        $data = Db::name('employee')
            ->alias('t1')
            ->join('d_role t2','t1.role_id = t2.role_id')
            ->join('d_province t3','t3.prov_num = t1.prov_num')
            ->join('d_city t4','t4.city_num = t1.city_num')
            ->join('d_area t5','t5.area_num = t1.area_num')
            ->where('emp_reg_time','>=',$dateMin===''?'1970-1-1 0:0:0':$dateMin)
            ->where('emp_reg_time','<=',$dateMax===''?date("Y-m-d H:i:s",time()+86400):$dateMax)
            //->where('t1.emp_name','like','%du%')
            ->order('t1.emp_id asc')
            ->field('t1.emp_id,t1.emp_name,t1.emp_status,t2.role_name,t3.prov_name,t4.city_name,t5.area_name')
            ->paginate($pageSize , false , ['type'=>'Hui']);
        $this->assign('list',$data); //绑定列表数据
        $this->assign('pageSize',$pageSize); //绑定分页数据
        $this->assign('dateMin',$dateMin); //绑定最小日期数据
        $this->assign('dateMax',$dateMax); //绑定最大日期数据
        $this->assign('keyword',''); //绑定关键字数据
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
        //$nowRoleId = (string)Session::get('nowRoleId');
        //if ($nowRoleId !== '1') return json('0');
        $id = input('get.edit','');
        $name = input('post.adminName', '');
        $nickname = input('post.adminNickname', '');
        $psw = input('post.password', '');
        $psw2 = input('post.password2', '');
        $province = input('post.province', '');
        $city = input('post.city', '');
        $area = input('post.area', '');
        $roleId = input('post.adminRole', '');
        date_default_timezone_set('PRC'); //设置时区
        $timestamp = time();  //当前时间戳
        if ($name === '' || $nickname === '' || $psw === '' || $psw2 === '' || $province === '' || $city === '' || $roleId === '') {
            return json('0');
        } else {
            if ($id) {
                $res = Db::name('employee')
                ->where('emp_id', $id)
                ->update([
                    'emp_psw' => $psw,
                    'emp_name' => $name,
                    'emp_nickname' => $nickname,
                    'role_id' => $roleId,
                    'prov_num' => $province,
                    'city_num' => $city,
                    'area_num' => $area,
                    'emp_head_img' => 'defaultHead.jpg' //isset($savePath['errorCode'])?'defaultHead.jpg':$savePath['success']
                ]);
            } else {
                //$path = $_SERVER['DOCUMENT_ROOT'].Request::instance()->root().'/static/img/upload'; //上传文件存储路径
                //$savePath = $this->saveUpload($_FILES['file-2'], $path, $name.$timestamp); //将上传文件保存
                $data = [
                    'emp_id' => '',
                    'emp_reg_time' => date('Y-m-d H:i:s', $timestamp),
                    'emp_psw' => $psw,
                    'emp_name' => $name,
                    'emp_nickname' => $nickname,
                    'role_id' => $roleId,
                    'prov_num' => $province,
                    'city_num' => $city,
                    'area_num' => $area,
                    'emp_status' => '使用',
                    'emp_head_img' => 'defaultHead.jpg' //isset($savePath['errorCode'])?'defaultHead.jpg':$savePath['success']
                ];
                $res = Db::name('employee')->insert($data);
            }
            if ($res === 1) {
                return json('1');
            } else {
                return json('0');
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

    //删除
    public function delete() {
        $id = input('get.id','');
        $res = Db::name('employee')->where('emp_id', $id)->delete();
        return json($res);
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