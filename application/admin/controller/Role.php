<?php

namespace app\admin\controller;

use think\Controller;           //引用官方封装的控制类
use think\Session;              //引用官方封装的Session类
use think\Cookie;               //引用官方封装的Cookie类
use think\Db;                   //引用官方封装的数据库单例类
use think\Request;               //引用变量获取使用

class Role extends Controller{

    //构造函数
    public function _initialize() {
        if (!sessionAssist('isLogin')) {
            $this -> redirect('Login/index');
        }
    }
    //错误方法访问
   /* function _empty(){
        $this -> redirect('Index/index');
    }*/
    //进入角色管理页面
    public function lists()
    {

        //表连接--角色表--用户表
        $roleList = DB::name('role dr')
            -> field('dr.*')
            -> select();

        //dump($roleList);

        $this -> assign('roleList', $roleList);
        return $this -> fetch();

    }

    //添加角色动作
    public function addTo(){
        //获取角色名称、备注
        $roleName = Request::instance()-> post('roleName');
        $remark = Request::instance()-> post('remark');

        //角色重名验证
        $select = Db::name('role')->where('role_name',$roleName)->find();

        if($select==[]){
            //定义一个事务变量
            $judge = false;
            Db::transaction(function() use($roleName,$remark,&$judge){
                //定义角色数组
                $roleData = ['role_name'=>$roleName,'role_description'=>$remark];

                $insert1 = Db::name('role')->insert($roleData);
                $roleId = Db::name('role')->getLastInsID();

                //先判断是否给角色赋予了初始化权限
                $isSetMenu = Request::instance()-> post('isSetMenu');
                if($isSetMenu){
                    //获取添加的权限子菜单
                    $prArr = Request::instance()-> post('prArr/a');
                    $roleMenuData = [];
                    foreach($prArr as $value){
                        array_push($roleMenuData,['role_id'=>$roleId,'sm_id'=>$value]);
                    }
                    //插入数据库----角色子菜单表---子菜单表
                    $insert2 = DB::name('role_menu')-> insertAll($roleMenuData);
                }else{
                    $insert2 =true;
                }
                if($insert1!==false && $insert2!==false){
                    $judge = true;
                }
            });
            if($judge){
                //成功
                return '添加角色成功！';
            }else{
                //回滚
                return '添加失败，请重试！';
            }
        }else{
            //角色名重复
            return '该角色已存在！';
        }
    }

    //读取所有菜单权限
    public function getMenu(){
        $fmenu = DB::name('fmenu fm')
            -> field('fm.fm_id,fm.fm_name')
            -> select();
        $smenu = DB::name('smenu sm')
            -> field('sm.fm_id,sm.sm_id,sm.sm_name')
            -> select();

        $menuList = [];

        for ($i = 0; $i < count($fmenu); $i++) {
            $arr = [
                'fm_id'  => $fmenu[$i]['fm_id'],
                'fm_name'  => $fmenu[$i]['fm_name'],
                'smenu' => []
            ];

            for ($j = 0; $j < count($smenu); $j++) {
                if ($fmenu[$i]['fm_id'] == $smenu[$j]['fm_id']) {
                    array_push($arr['smenu'], $smenu[$j]);
                }
            }

            if (count($arr['smenu'])) {
                array_push($menuList, $arr);
            }
        }

        return $menuList;

    }

    //进入添加角色页面
    public function add(){
        return $this -> fetch();
    }

    //删除角色
    public function del(){
        $roleId = Request::instance()->post('roleId');
        //定义一个事务变量
        $judge = false;
        Db::transaction(function() use($roleId,&$judge){
            Db::name('role_menu')->where('role_id',$roleId)->delete();
            Db::name('role')->where('role_id',$roleId)->delete();
            $judge = true;
        });
        if($judge){
            return 1;
        }else{
            return 0;
        }
    }
    //批量删除
    public function delMany(){
        $idarr = Request::instance()->post('idarr/a');
        //定义一个事务变量
        $judge = false;
        foreach ($idarr as $roleId) {
            Db::transaction(function() use($roleId,&$judge){
            Db::name('role_menu')->where('role_id',$roleId)->delete();
            Db::name('role')->where('role_id',$roleId)->delete();
            $judge = true;
        });
        }
        
        if($judge){
            return 1;
        }else{
            return 0;
        }
    }

    //进入修改角色页面
    public function edit(){
        return $this -> fetch();
    }
    //获取角色对应的菜单权限
    public function getRoleMenu(){
        $roleId = Request::instance()->post('roleId'); 
        //寻找角色对应的子菜单
        $smenu = DB::name('role_menu')
            -> alias('rm')
            -> join('smenu sm','sm.sm_id=rm.sm_id')
            -> field('sm.fm_id,sm.sm_id,sm.sm_name')
            -> where('rm.role_id',$roleId)
            -> select();
        //dump($smenu);
        $fmIdArr = DB::name('role_menu')
            -> alias('rm')
            -> join('smenu sm','sm.sm_id=rm.sm_id')
            -> field('sm.fm_id')
            -> where('rm.role_id',$roleId)
            -> group('sm.fm_id')
            -> select();
        //dump($fmIdArr);
        $fmenu = []; 
        for ($i = 0; $i < count($fmIdArr); $i++) {
            $fm = DB::name('fmenu fm')
            -> field('fm.fm_id,fm.fm_name')
            -> where('fm.fm_id',$fmIdArr[$i]['fm_id'])
            -> find();
            array_push($fmenu,$fm);
        }
        //dump($fmenu);

        $menuList = [];

        for ($i = 0; $i < count($fmenu); $i++) {
            $arr = [
                'fm_id'  => $fmenu[$i]['fm_id'],
                'fm_name'  => $fmenu[$i]['fm_name'],
                'smenu' => []
            ];

            for ($j = 0; $j < count($smenu); $j++) {
                if ($fmenu[$i]['fm_id'] == $smenu[$j]['fm_id']) {
                    array_push($arr['smenu'], $smenu[$j]);
                }
            }

            if (count($arr['smenu'])) {
                array_push($menuList, $arr);
            }
        }

        $allMenu = $this->getMenu();

        
        //角色权限，所有权限，角色信息        
        return [$menuList,$allMenu];

    }

    //获取角色信息
    public function getRoleMsg(){
        $roleId = Request::instance()->post('roleId'); 
        $roleMsg = Db::name('role')
            -> where('role_id',$roleId)
            -> find();
        return json_encode($roleMsg);
    }

    //修改角色
    public function editRole(){
        //获取角色名称、备注
        $roleName = Request::instance()-> post('roleName');
        $remark = Request::instance()-> post('remark');
        $roleId = Request::instance()-> post('roleId');

        //角色重名验证
        $select = Db::name('role')->where('role_name',$roleName)->find();

        if($select==[] || $select['role_id']==$roleId){
            //定义一个事务变量
            $judge = false;
            Db::transaction(function() use($roleName,$remark,$roleId,&$judge){
                //定义角色数组
                $roleData = ['role_name'=>$roleName,'role_description'=>$remark];

                $update = Db::name('role')->where('role_id',$roleId)->update($roleData);
                

                //先删除该角色的所有权限
                $delete = Db::name('role_menu')
                    ->where('role_id',$roleId)
                    ->delete();


                //先判断是否给角色赋予了初始化权限
                $isSetMenu = Request::instance()-> post('isSetMenu');

                if($isSetMenu){
                    //获取添加的权限子菜单
                    $prArr = Request::instance()-> post('prArr/a');
                    $roleMenuData = [];
                    foreach($prArr as $value){
                        array_push($roleMenuData,['role_id'=>$roleId,'sm_id'=>$value]);
                    }
                    //插入数据库----角色子菜单表---子菜单表
                    $insert2 = DB::name('role_menu')-> insertAll($roleMenuData);
                }else{
                    $insert2 =true;
                }
                if($update!==false && $insert2!==false && $delete!==false){
                    $judge = true;
                }
            });
            if($judge){
                //成功
                return '修改角色成功！';
            }else{
                //回滚
                return '修改失败，请重试！';
            }
        }else{
            //角色名重复
            return '该角色已存在！';
        }
    }

}