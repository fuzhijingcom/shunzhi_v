<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;
use app\work\logic\YuangongLogic;

use think\Page;
use think\Db;
class Repair extends MobileBase {

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
        $grade =  M('yuangong') ->where("yid",$this->user_id)->getField('grade');     
        if(!$grade ){
            $this->redirect('work/error/noauth', array('errmsg' => '无权限'), 2 , '页面跳转中...');
            exit;
        }
        
    }
   
    public function index(){
       
       $list = M('repair')->order('id desc')->select();
        $this->assign("list",$list);
        return $this->fetch();
    }
    
   
}