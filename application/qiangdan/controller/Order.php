<?php
namespace app\qiangdan\controller;
use app\qiangdan\logic\QiangLogic;

use think\Db;
use think\Page;

class Order extends MobileBase {
    
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
        $user_id = session('user.user_id');
        if(!user_id){
            $this->error('请先登录');
            exit;
        }
        $this->redirect('order_list');
    }
    
    public function order_list()
    {
        $condition['order_id'] = array('gt',14450);
        $condition['qiang'] = array('eq', 1 );
        $condition['pay_status'] = array('eq', 1 );
        $condition['order_status'] = array('eq', 0 );
        
        $list = M('kd_order')->where($condition)->order('order_id desc')->select();
        $this->assign('list', $list);
        
        $this->assign('list_count', count($list));
        
        
        $grade =  M('yuangong') ->where("yid",$this->user_id)->getField('grade');
        $this->assign('grade',$grade);
        return $this->fetch();
    }
    
    public function my_order()
    {
        $user_id = session('user.user_id');
        
       // $condition['order_id'] = array('gt',13000);
        $condition['qiang'] = array('eq', 1 );
        $condition['pay_status'] = array('eq', 1 );
        
        $type = I('type');
        if(!$type){
            $type = 6 ;
            $limit = 30;
        }
        
        if($type == 7){
            $limit = 5;
        }
        
        $condition['order_status'] = $type ;
        
        $this->assign('type', $type);
        
        $condition['receiver'] = $user_id;
        
        $list = M('kd_order')->where($condition)->limit($limit)->order('receivetime desc')->select();
       
        $this->assign('list', $list);
        
       
        return $this->fetch();
    }
    
    /*
     * 已经派送，待确认订单
     */
    public function songda()
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
            $this->error('订单状态出错');
            exit;
        }
         
        $receivetime = date('Y-m-d H:i:s');
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '已送达，抢单员：'.$user_id,
            'status_desc'     =>'订单已送达',
            'log_time'        =>$receivetime,
        );
        M('kd_order_action')->insertGetId($action_info);
        
        
        $songda_time = date('Y-m-d H:i:s');
        $extra = M('kd_order_extra')->where(array('order_id'=>$order_id))->find();
        if(!$extra){
        M('kd_order_extra')->save(array('order_id'=>$order_id,'songda_time'=> $songda_time));
        }else{
            M('kd_order_extra')->where(array('order_id'=>$order_id))->save(array('order_id'=>$order_id,'songda_time'=> $songda_time));
        }
        
        
       

        $logic = new QiangLogic();
        $data = $logic->push_msg_songda($order_id);
        $data = json_decode($data,true);
        
       
        
        if ($data['errcode'] == 0){
            
            $kouchu = M('kd')->where(array('type'=>$order['type']))->getField('money');
            $money = (float)$order['order_amount'] - (float)$kouchu;
            $payresult = accountLog($user_id,$money,$money,$order_id."收入".$money."元,增加".$money."积分");
            
            if($payresult == true){
                
                
                $data['order_id'] = $order_id;
                $data['action_user'] = $user_id;
                $data['action_note'] = '已经配送完成';
                $data['order_status'] = 7;
                $data['log_time'] = $peisong_time;
                $data['status_desc'] = '已经配送';
                M('kd_order_action')->add($data);//订单操作记录
                
                M('kd_order')->where(array('order_id'=>$order_id))->save(array('order_status'=> 7));//订单操作记录
                
                $this->success('已经成功通知客户，佣金已到账',U('/qiangdan/order/my_order'));
            }else{
            	$this->error('服务器出错，到账出错'.$payresult);
            }
            
        }else {
        	
        	$this->error('服务器出错'.$data['errcode'].$data['errmsg'],U('/qiangdan/order/my_order'));
        }
    }
    
     /*
     * 订单详情
     */
    public function order_detail()
    {
        $user_id = session('user.user_id');
        $order_id = I('get.order_id/d');
        
        $map['order_id'] = $order_id;
       
        $order_info = M('kd_order')->where($map)->find();
        $order_info = set_btn_order_status($order_info);  // 添加属性  包括按钮显示属性 和 订单状态显示属性
        
        if (!$order_info) {
            $this->error('没有获取到订单信息');
            exit;
        }

        if($order_info['qiang'] !== 1){
            $this->error('此订单不能抢');
            exit;
        }
        
        if($order_info['order_status'] == 3 ){
            $this->error('用户取消订单');
            exit;
        }
        
        if($order_info['order_status'] !== 0){
            
            

            $grade =  M('yuangong') ->where("yid",$user_id)->getField('grade');
            
            if($grade >= 5 ){
                $this->redirect("qiangdan/admin/index",array('id'=>$order_id));
                exit;
            }else{
                 
                
            $this->error('订单已经被抢','qiangdan/order/index');
            exit;
            
            }
        }
        
        
        
        $region_list = get_region_list();
        $invoice_no = M('DeliveryDoc')->where("order_id", $id)->getField('invoice_no', true);
        $order_info[invoice_no] = implode(' , ', $invoice_no);
        //获取订单操作记录
        $order_action = M('kd_order_action')->where(array('order_id' => $id))->select();
       
        $this->assign('order_status', C('ORDER_STATUS'));
        $this->assign('shipping_status', C('SHIPPING_STATUS'));
        $this->assign('pay_status', C('PAY_STATUS'));
        $this->assign('region_list', $region_list);
        $this->assign('order_info', $order_info);
        $this->assign('order_action', $order_action);

        if (I('waitreceive')) {  //待收货详情
            return $this->fetch('wait_receive_detail');
        }
        
        
        $grade =  M('yuangong') ->where("yid",$user_id)->getField('grade');
        $this->assign('grade',$grade);
        return $this->fetch();
    }

   public function qiang(){
       $user_id = session('user.user_id');
       
       $qun = M('users_qiang')->where('user_id',$user_id)->getField('qun');
       
       if($qun  == 0){
           $this->error('抢单前，先加入微信群，才可以抢单','qiangdan/index/index');
           exit;
       }
       
       
       $order_id = I('get.order_id/d');
       $map['order_id'] = $order_id;
       $order_info = M('kd_order')->where($map)->find();
       
       if($order_info['order_status'] !== 0){
         
           $this->error('该订单已经被抢');
           exit;
           
       }
       
      
       /*
       $condition['receiver'] = $user_id;
       $retime = date('Y-m-d');
       $condition['receivetime'] =  array('like','%'.$retime.'%');
       $condition['order_status'] = 6;
       $condition['qiang'] = 1;
       
       
       if($user_id !== 888 || $user_id !== 9720 ){
       
           $yiqiang =  M('kd_order')->where($condition)->count();
            
           if($yiqiang > 11){
               $this->error('抢单失败，你已经抢了'.$yiqiang.'个。');
               exit;
           }
       }
      
       
       if($order_info['type'] !== 'wm'){
           $hour=(int)date("G");
           if($hour <=10){
                $this->error('时间没到，11点开始');
               exit;
           }
       }
        
        */
       
       $receivetime = date('Y-m-d H:i:s');
        $result = M('kd_order')->where('order_id',$order_id)->save(['receiver' => $user_id,'receivetime' => $receivetime, 'order_status' => 6 ]);;
        
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$uid,
            'action_note'     => '已接单，时间：'.$receivetime,
            'status_desc'     =>'抢单者：'.$uid, 
            'log_time'        =>$receivetime,
        );
        M('kd_order_action')->insertGetId($action_info);
        
        $receivetime = date('Y-m-d H:i:s');
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '已抢，抢单员：'.$user_id,
            'status_desc'     =>'订单已抢',
            'log_time'        =>$receivetime,
        );
        M('kd_order_action')->insertGetId($action_info);
        
        $logic = new QiangLogic();
        $data = $logic->push_msg_qiang($order_id);
       

        if ($result == 1) {
            $this->success('抢单成功','qiangdan/order/index');
        }
       
   }
   
   
  
}