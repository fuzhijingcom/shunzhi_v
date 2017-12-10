<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\work\logic\WorkjijianLogic;

use think\Page;
use think\Db;
class Jijian extends MobileBase {

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
                  
        if(!$grade || $grade < 1  ){
             $this->redirect(U("work/error/noauth"));
            exit;
        }
        
    }
    

   
        
        
    public function index(){
    	
    	$condition['order_id'] = array('gt',16978);
    	$condition['pay_status'] = array('eq',1);
    	$condition['type'] = array('eq','ji');
    	
    	$list6 = D('kd_order')->where($condition)->where('order_status',6)->field('order_id')->order('order_id desc')->select();
    	$list7 = D('kd_order')->where($condition)->where('order_status',7)->field('order_id')->order('order_id desc')->select();
    	
    	
    	$con['order_id'] = array('gt',16978);
    	$con['pay_status'] = array('eq',0);
    	$con['type'] = array('eq','ji');
    	$list8 = D('kd_order_ji')->where($con)->where('order_status',8)->field('order_id')->order('order_id desc')->select();
    	
    	$this->assign('count6', count($list6));
    	$this->assign('count7', count($list7));
    	$this->assign('count8', count($list8));
    	
    	//已完成，未付款
    	return $this->fetch();
    }

    public function read(){
    
     	$uid = session('user.user_id');
        if ($uid == null) {
            $this->error('请先登录');
        }
        $username = session('user.nickname');
    
        $order_status = I('order_status');
        if($order_status == 8){
        	//完成未付款
        	$pay_status = I('pay_status');
        	$this->assign('pay_status', $pay_status);
        	
        	$condition['pay_status'] = array('eq',$pay_status);
        	
        	$database = D('kd_order_ji');
        	//读取ji表
        }else{
        	$condition['pay_status'] = array('eq',1);
        	$database = D('kd_order');
        }
	        $condition['order_id'] = array('gt',16978);
	        $condition['order_status'] = array('eq',$order_status);
	        $condition['type'] = array('eq','ji');
        
	        $read = $database ->where($condition)->order('order_id desc')->select();
    
        $this->assign('order_status', $order_status);
        $this->assign('read', $read);
        return $this->fetch();
    }
    
   public function detail(){
   		$order_id  = I('order_id');
   		
   		$condition['order_id'] = $order_id;
   		
   		$order= D('kd_order')->where($condition)->find();
   		$this->assign('order', $order);
   		
   		$order_ji= D('kd_order_ji')->where($condition)->find();
   		$this->assign('order_ji', $order_ji);
   		
   		//dump($order);
   		//dump($order_ji);
   		
   		$this->assign('order_id', $order_id);
   		return $this->fetch();
   }
   
   public function send(){
   		$order_id  = I('order_id');
   		
   		$order_amount  = I('order_amount');
   		
   		$shipping_time= date('Y-m-d H:i:s');
   		
   		$admin_note  = I('admin_note');
   		
   		$data = array(
   				'order_amount'=>$order_amount,
   				'admin_note'=>$admin_note,
   				'pay_status'=> 0 ,
   				'order_status'=>8,
   				'shipping_time'=> $shipping_time
   		);
   		
   		
   		
   		$logic = new WorkjijianLogic();
   		$send = $logic->push_msg_send_dh_to_user($order_id,$admin_note,$order_amount);
   		$send = json_decode($send,true);
   		
   		if($data['errcode'] == 0){
   			
   			D('kd_order_ji')->where('order_id',$order_id)->data($data)->save();
   			
   			D('kd_order')->where('order_id',$order_id)->data(array('order_status'=>8,'shipping_time'=> $shipping_time ))->save();
   			
   			$this->success('发送成功','work/jijian/index');
   		}else {
   			$this->error('发送失败');
   			
   			
   		}
   		
   }
 
}