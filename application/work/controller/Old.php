<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use think\Page;
use think\Db;
class Old extends MobileBase {

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
            $uid = $user['user_id'];
        }
       
        $grade =  M('yuangong') ->where("yid",$uid)->getField('grade');

        if($grade==0){
             $this->redirect(U("work/error/noauth"));
            exit;
        }
    }
    
    
    public function index(){
      

        $uid = session('user.user_id');
        if ($uid == null && is_weixin() == true) {
           $this->error('还没登录');
            exit;
        }
        if ($uid == null && is_weixin() == false) {
            //header("Location: " . "http://www.yykddn.com/mobile" );
            //exit;
        }
        $username = session('user.nickname');

        $this->redirect(U("search"));

        // return $this->fetch();
    
    }
    
    public function search(){

        $searchrealname= $_POST["searchrealname"];
          if($searchrealname == NULL){
              $searchrealname = '请输入名字';
          }

        $this->assign('searchrealname', $searchrealname);
 
        $condition['consignee'] = array('like','%'.$searchrealname.'%');
        $condition['pay_status'] = array('eq',1);
        $read = D('kd_order')->where($condition)->order('order_id desc')->select();

        $this->assign('read', $read);
        return $this->fetch();
    }
    
    public function detail(){
    
       $order_id = I('order_id');
       
 
        $read = D('kd_order_action')->where(array('order_id' => $order_id))->select();
   
       
        $this->assign('order_id', $order_id);
        $this->assign('read', $read);
        return $this->fetch();
    }
    
    
   
    
}