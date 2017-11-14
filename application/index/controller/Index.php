<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller {
    public function index() {
        return $this -> fetch();
    }

    public function taxi() {
        return $this -> fetch();
    }

    public function privateTaxi() {
        return $this -> fetch();
    }

    public function aboutUs() {
        return $this -> fetch();
    }

    public function contactUs() {
        return $this -> fetch();
    }
}
