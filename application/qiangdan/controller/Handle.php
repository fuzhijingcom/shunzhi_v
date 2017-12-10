<?php
namespace app\qiangdan\controller;
use app\qiangdan\logic\QiangLogic;

use think\Db;
use think\Page;

class Handle extends MobileBase {
    
    public $user_id = 0;
    public $user = array();
    /**
     * 析构流函数
    */
    public function  __construct() {
        parent::__construct();
        
        if(session('?user'))
        {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user',$user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user',$user); //存储用户信息
             
        }
        
        $is_validated =  M('users_qiang') ->where("user_id",$this->user_id)->getField('is_validated');
        if(!$is_validated || $is_validated !== 1  ){
            $this->error('你还不是自由快递员');
            exit;
        }
    
    }
    
    
    public function index(){
       
    }
    
    
    
    /*
     * 申请退单
     */
    public function tui()
    {
        
        $user_id = session('user.user_id');
        if (!$user_id){
           $this->error('未登录，登录过期');
           exit;
        }
    
        $order_id = I('get.id/d');
        $condition['order_id'] = $order_id ;
        $condition['qiang'] = 1 ;
        $condition['pay_status'] = 1;
        $condition['receiver'] = $user_id;
        
        $order = M('kd_order')->where($condition)->find();

        //检查是否未支付订单 
        if(!$order){
            $this->error('订单不存在');
            exit;
        }
        //检查是否未支付的订单
        if( $order['order_status'] !== 6 ){
            $this->error('订单状态出错，不能退单');
            exit;
        }
         
        $handle = M('kd_order_handle')->where(array('order_id'=>$order_id))->find();
        $this->assign('reason',$handle['reason']);
        
        
        if(IS_POST){
            $reason = I('reason');
            if($reason == NULL){
                $this->error('原因不能为空');
                exit;
            }
            
            
            $extra = M('kd_order_handle')->where(array('order_id'=>$order_id))->find();
            if(!$extra){
            	M('kd_order_handle')->save(array('order_id'=>$order_id,'receiver'=>$user_id,'reason'=> $reason,'status'=>0));
            }else{
            	M('kd_order_handle')->where(array('order_id'=>$order_id))->save(array('reason'=> $reason,'receiver'=>$user_id,'status'=>0));
            }
            
            $this->success('等待审批','qiangdan/order/my_order');
        }
        
        
      return $this->fetch(); 
        
    }
    
     
   
  
}