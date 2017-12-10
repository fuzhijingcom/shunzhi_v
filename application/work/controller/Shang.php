<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use think\Page;
use think\Db;
class Shang extends MobileBase {

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
        $read = D('kd_order1')->where($condition)->order('order_id desc')->select();

        $this->assign('read', $read);
        return $this->fetch();
    }
    
    public function detail(){
    
       $order_id = I('order_id');
       
 
        $read = D('kd_order_action1')->where(array('order_id' => $order_id))->select();
   
       
        $this->assign('order_id', $order_id);
        $this->assign('read', $read);
        return $this->fetch();
    }
    
    
    public function wentijian(){
       
        if (!$this->user_id) {
            $this->error('还没登录');
            exit;
        }
         
        //  $condition['add_time'] = array('like','%'.date('Y-m-d').'%');
        //where($condition)->
    
        $condition['order_id'] = array('gt',15920);
        $condition['order_status'] = array('neq',7);
        $con['order_status'] = array('neq',5);
        $co['order_status'] = array('neq',1);
         
        $pay_order = M('kd_order')->where(array('pay_status'=>1))->where($condition)->where($con)->where($co)->select();
        $c = count($pay_order);
        $this->assign('c',$c);
        $this->assign('type',$type);
        $this->assign('pay_order',$pay_order);
        return $this->fetch();
    }
   
   
    public function tousu_order(){
        $order_id = I('id');
        //把状态改成12
        if(IS_POST){
            $desc = I('desc');

           
            
            $data=array(
                'parent_id' => 0,
                'user_id' => $this->user_id,
                'user_name' => session('user.nickname'),
                'msg_title' => '订单'.$order_id.'投诉',
                'msg_type' => 1,
                'msg_status' => 0,
                'msg_content' => $desc,
                'msg_time' => time(),
                'msg_img' => 0,
                'order_id' => $order_id,
               'msg_area' => 0,
            );
            M('feedback')->save($data);
            
            
            M('kd_order')->where("order_id",$order_id)->save(array('order_status'=>12));
            
            $logic = new Jssdk();
            $content = '订单'.$order_id.'投诉:'.$desc.'。订单号：.'.$order_id.'进入问题件处理列表查看：http://www.yykddn.com/mobile/kefu/wentijian';
           
            $user = M('admin_sj')->field('openid')->where(array('is_kf' => 1 , 'is_validated' => 1 ))->select();
       
            $c = count($user);
            $pushlogic = new Push();
            
            for ($i = 0; $i < $c; $i++){
                $openid =  $user[$i]['openid'];

                $data = $logic->push_msg($openid,$content);
               
            }
            
            
           
            
        }
      
        
        
        
        $is_show =  M('feedback')->where("order_id",$order_id)->find();
        
        if(!$is_show){
            $this->assign('is_show',1);
        }else {
             $this->assign('is_show',0);
        }
        
        return $this->fetch();
    }
    
}