<?php
/** 
 * 通知chat聊天模块
 */
namespace app\chat\controller;
use app\home\logic\UsersLogic;


use think\Page;
use think\Db;
class Message extends MobileBase {

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
            $this->assign('user_id',$user['user_id']);
        }
    }
    
       
    public function index(){
        
        $id = I('id');
       
        
        $user_id = session('user.user_id');
        
        $sql = "SELECT * FROM `tp_chat` WHERE ( `send` = ".$user_id." and `receive` = ".$id." ) or ( `receive` = ".$user_id." and `send` = ".$id." )";
        
       $jieguo =  Db::query($sql);
      
       
       $this->assign('receive',$id);
       $this->assign('send',$user_id);
        
       
        $this->assign('jieguo',$jieguo);
       
        return $this->fetch();
    }
        
   
     
   
}