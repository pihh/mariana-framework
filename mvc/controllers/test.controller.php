<?php

use Mariana\Framework\Controller;

class TestController extends Controller{

    public function index(){
        echo "First Level (/)";
        $s = new Sessions();
        $u = new Users;
        $u->name = "XXXYYY";
        $u->id = "8";
        $u->save();
        echo "<pre>";
        //print_r($result);
    }

    public function index_2(){
        echo "First Level (/home/)";
    }

    public function index_3(){
        echo "Second Level (/home/user/)";
    }

    public function index_4(){
        echo "Third Level (/home/{}/) with params: ";
        echo '<pre>';
        print_r($this->params);
    }

    public function index_5(){
        echo "Third Level (/home/user/{}) with params: ";
        echo '<pre>';
        print_r($this->params);
    }
}