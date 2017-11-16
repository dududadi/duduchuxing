<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;

class Employee extends Controller{
    public function lists() {
        return $this->fetch();
    }

    //查询员工列表数据
    public function getEmployeeLists() {
        $data = Db::name('employee')
        ->alias('t1')
        ->join('d_role t2','t1.role_id = t2.role_id')
        ->join('d_province t3','t3.prov_num = t1.prov_num')
        ->join('d_city t4','t4.city_num = t1.city_num')
        ->join('d_area t5','t5.area_num = t1.area_num')
        ->order('t1.role_id asc')
        ->column('t1.emp_id,t1.emp_name,t1.emp_status,t2.role_name,t3.prov_name,t4.city_name,t5.area_name');
        return json($data);
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
}
?>