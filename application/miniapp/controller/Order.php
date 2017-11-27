<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/11/26
 * Time: 16:18
 */

namespace app\miniapp\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Order extends Controller{
    public function orderList()
    {
        $res = Db::name('order_list')
            ->select();

        echo json_encode($res);
        exit;
    }

}