<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;
use app\work\logic\QiangLogic;

use think\Page;
use think\Db;
class Qiang extends MobileBase {

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
        if(!$grade || $grade < 6  ){
            $this->error('权限不够');
            exit;
        }
    }
   
    public function index(){
        $id = I('id');
        if($id){
            $order_info = M('kd_order')->where('order_id',$id)->find();
            $this->assign('order_info',$order_info);
        }
        
        
        $handle = M('kd_order_handle')->where('status',0)->select();
        $this->assign('tui',count($handle));
        
       return $this->fetch();
    }
    
    public function detail(){
    	$id = I('id');
    	if($id){
    		$order_info = M('kd_order')->where('order_id',$id)->find();
    		$this->assign('order_info',$order_info);
    	}
    	
    	return $this->fetch();
    }
    
    
    
    public function history(){
    	
    	$history= M('kd_order_handle')->where('status',1)->order('order_id desc')->select();
    	$this->assign('history',$history);
    	
    	return $this->fetch();
    	
    }
    
    
   public function change(){
       $id = I('id');
       
    
       $qiang = I('qiang');
       $order = M('kd_order')->where('order_id',$id)->find();
       
       //判断订单，可不可以取消
       $dangqian_status = $order['order_status'];
       if($dangqian_status == 1){
           $this->error('订单已拿了，不能转换');
       }
      
       if($dangqian_status == 7){
           $this->error('订单已送达，不能转换');
       }
       
       $uid = $order['user_id'];
       $uid_receiver= $order['receiver'];
       $openid = M('users')->where('user_id',$uid)->getField('openid');
       $name = session('user.nickname');
       
       $order_status = I('order_status');
       $action = I('action');
       
       if( $order_status == 1 && $action == 'yn'){
           $status = '转成已拿';
           $beizhu = '抢单员取消订单。订单已交给驿源团队派送，晚上送到';
           $receiver = 0;
           
       }elseif($order_status == 0 && $action == 'sf'){
           //释放订单
           
           $status = '转成未拿';
           $beizhu = '释放订单，恢复成待抢状态。等待下一位抢单员抢单。';
           $receiver = null;
           
       }elseif($order_status == 0 && $action == 'zbd'){
           //释放订单
           
           $status = '找不到，可取消订单';
           $beizhu = '抢单员说：你的快递找不到。可以取消订单，重新下单';
           $receiver = null;
           
       }elseif($order_status == 0 && $action == 'hh'){
           //释放订单
           
           $status = '需要补充货号';
           $beizhu = '抢单员说：你的快递无货号。无法代拿。请取消订单，重新下单';
           $receiver = null;
           
       }else{
       
       $status = '转成未拿';
       $beizhu = '您的快递恢复成未拿状态。如有下单错误，可以取消订单、重新下单';
       $receiver = null;
       }
       
       
       $extra = M('kd_order_extra')->where(array('order_id'=>$id))->find();
       if(!$extra){
           M('kd_order_extra')->save(array('order_id'=>$id,'beizhu'=> $status.'，客服：'.$name));
       }else{
           M('kd_order_extra')->where(array('order_id'=>$id))->save(array('beizhu'=> $status.'，客服：'.$name));
       }
       
       $receivetime = date('Y-m-d H:i:s');
       $action_info = array(
           'order_id'        =>$id,
           'action_user'     =>$uid,
           'action_note'     => $status.'，客服：'.$name,
           'status_desc'     =>$receivetime,
           'log_time'        =>$receivetime,
       );
       M('kd_order_action')->insertGetId($action_info);
       
       M('kd_order')->where(array('order_id'=>$id))->save(array('receiver'=> $receiver,'order_status'=>$order_status,'qiang'=>$qiang));
       
        $logic = new QiangLogic();
        $data = $logic->change($id,$openid,$name,$status,$beizhu);
       
        //提醒抢单员
        $receiveropenid = M('users_qiang')->where(array('user_id'=>$order['receiver']))->getField('openid');
        
        
        $logic1 = new QiangLogic();
        $logic1->change_receiver($id,$receiveropenid,$name,$status,$beizhu);
        
        $extra = M('kd_order_handle')->where(array('order_id'=>$id))->find();
        if(!empty($extra)){
            M('kd_order_handle')->where(array('order_id'=>$id))->save(array('status'=> 1));
        }
        $kou = I('kou');
        if($kou == '1'){
	        //登记退单次数
	         M('users_qiang')->where(array('user_id'=> $uid_receiver))->setDec('credit',1);
        }
        
        $this->success('更改成功','work/qiang/index');
   }
    
   public function update_order_note(){
       $order_id = I('order_id');
       $user_note = M('kd_order')->where('order_id',$order_id)->getField('user_note');
       $this->assign('user_note',$user_note);
       
       if(IS_POST){
           $data['user_note'] = I('user_note');
           M('kd_order')->where('order_id',$order_id)->save($data);
           $this->success('修改成功','qiangdan/order/order_detail?order_id='.$order_id);
       }
       return $this->fetch();
   }
   
   
   public function tui(){
       $handle = M('kd_order_handle')->where('status',0)->select();
       
       
       $this->assign('handle',$handle);
       $this->assign('tui',count($handle));
       return $this->fetch();
   }
   
   
   public function delete_shenqing() {
       $id = I('id');
       $order = M('kd_order')->where('order_id',$id)->find();
       
       M('kd_order_handle')->where(array('order_id'=>$id))->save(array('status'=> 2));
       $this->success('更改成功','work/qiang/index');
   }
}