<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\work\logic\WorkLogic;

use think\Page;
use think\Db;
class Box extends MobileBase {

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
    

   
        
        
    

    public function read(){
    
     $uid = session('user.user_id');
        if ($uid == null) {
            $this->error('请先登录');
        }
            
        $username = session('user.nickname');
    
        $searchrealname= $_POST["searchrealname"];
      
        $this->assign('searchrealname', $searchrealname);
         
        $type= 'box';
        $this->assign('type', $type);
         
   
        $date = $_POST["date"];
       
        //dump($date);
        $this->assign('date', $date);
        $order_status = $_POST["order_status"];
        if($order_status == NULL) {
            $order_status = 0 ;
        }
    
        if( $order_status == 1 ){
           if($date == NULL){
                $date = date("Y-m-d");
                //改了，不传过来就是全部
            }
        }
        
        $this->assign('order_status', $order_status);
 

        $condition['consignee'] = array('like','%'.$searchrealname.'%');
        $condition['add_time'] = array('like','%'.$date.'%');
        $condition['pay_status'] = array('eq',1);
        if($type != 'today'){
            $condition['type'] = array('eq',$type);
        }
    
        $condition['order_status'] = array('eq',$order_status);

        $read = D('kd_order_box')->where($condition)->select();
    
        $todaytime = date("Y-m-d");
        $todaycondition['add_time'] = array('like','%'.$date.'%');
         
    
       // $today_time_first =  D('kd_order') -> order('order_id')->limit(1)->where($todaycondition)->select();
        // dump($today_time_first['0']['id']);
        //$this->assign('today_time_first', $today_time_first['0']['id']);
        
       
        $this->assign('read', $read);
        return $this->fetch();
    }
    
    
   
    
    public function qianshou() {
        
        $uid = session('user.user_id');
        if ($uid == null) {
              $this->error('请先登录');
        }
    
        $this->assign('uid', $uid);
        $receivetime = date("Y-m-d H:i:s");
       
        $type = $_GET['type'];
        
       
        $this->assign('kuaiai', $qianshou);

        $order_id =  I('order_id');

        
        if (IS_POST) {
            
            //检查是否未支付的订单
            $order_status_check = M('kd_order_box')->where(array('order_id'=>$order_id))->getField('order_status');
            if( $order_status_check > 0){
               $this->redirect(U("box/kuaidi/read" , array('type' => $type  )));
                exit;
            }
             
            
            
            
            
            $result = M('kd_order_box')->where('order_id',$order_id)->save(['receiver' => $uid,'receivetime' => $receivetime, 'order_status' => 1 ]);;
        
            $action_info = array(
                'order_id'        =>$order_id,
                'action_user'     =>$uid,
                'action_note'     => '订单已拿，时间：'.$receivetime,
                'status_desc'     =>'签收者：'.$uid, 
                'log_time'        =>date('Y-m-d H:i:s'),
            );
            M('kd_order_action')->insertGetId($action_info);
            
           
            $logic = new WorkLogic();
            $data = $logic->push_msg_yina_box($order_id,$uid,$receivetime);
             
            
            if ($result == 1) {
                $this->redirect(U("work/box/read" , array('type' => $type  )));
            }
            $this->error('签收失败，请联系精哥哥！');
            return;
        }
        
        
        $kd_order = M('kd_order_box')->where('order_id',$order_id)->find();
       
       
        if(!isset($kd_order)){
            $this->error('快递信息不存在，请重新选择！');
            return;
        }
        
        $user_note = $kd_order['user_note'];
        //dump($user_note);
        
        $code = "http://s.jiathis.com/qrcode.php?url=".$user_note;
        
        $this->assign('code',$code);
        
       // D('kd_order')->select();
        $this->assign('order',$kd_order);
       
       
        return $this->fetch();
    }
    
    
    
    
    
   
 
}