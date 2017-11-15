<?php
namespace app\admin\controller;

use think\Controller;

class Index extends Controller {
    public function index() {
        return $this -> fetch();
    }

    public function welcome() {
        return $this -> fetch();
    }

    public function articleList() {
        return $this -> fetch('index/article-list');
    }

    public function aboutUs() {
        return $this -> fetch();
    }

    public function contactUs() {
        return $this -> fetch();
    }
}
