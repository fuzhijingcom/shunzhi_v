<?php
namespace app\qiangdan\controller;
use app\home\logic\UsersLogic;

use think\Page;
use think\Db;
class Admin extends MobileBase {

    public $user_id = 0;
    public $user = array();
    /**
     * 析构流函数
    */
    public function  __construct() {
        parent::__construct();
       // $this->cartLogic = new \app\home\logic\CartLogic();
        if(session('?user'))
        {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user',$user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user',$user); //存储用户信息
           
        }
   
    }
    public function index(){
       
        $order_id = I('id');
        $order_info = M('kd_order')->where('order_id',$order_id)->find();
        
        
        $this->assign('order_info',$order_info);
        return $this->fetch();
        
    }
   
    
}