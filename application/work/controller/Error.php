<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;

use think\Page;
use think\Db;
class Error extends MobileBase {

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
   
   
    public function noauth(){
        
      
        $sessionuid = session('user.user_id');
      
       
        $this->assign('id',$sessionuid);
        
        
        $errmsg = I('errmsg');
        
        $this->assign('errmsg',$errmsg);
        return $this->fetch();
    }
    
    public function iderror(){
       
        header("Content-type:text/html;charset=utf-8");
        $user_id =  session('user.user_id');
       
        $this->assign('id',$user_id);
        return $this->fetch();
    }
    
    
}