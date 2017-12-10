<?php
namespace app\qiangdan\controller;
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
   
    }
    

   
        
        
    public function order_list(){
      
        $user_id = session('user.user_id');
        
        
        //$where['user_id'] = $user_id;
        //$where['pay_status'] = 1 ;
        $where['order_status'] = 0 ;
        //条件搜索
        $order_status = I('get.order_status');
        if($order_status!==''){
            $where['order_status'] = $order_status;;
        }
        $count = M('kd_order_box')->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $order_str = "order_id DESC";
        $order_list = M('kd_order_box')->order($order_str)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
    
     
        
        $this->assign('order_status', C('ORDER_STATUS'));
        $this->assign('shipping_status', C('SHIPPING_STATUS'));
        $this->assign('pay_status', C('PAY_STATUS'));
        $this->assign('page', $show);
        $this->assign('lists', $order_list);
        $this->assign('active', 'order_list');
        $this->assign('active_status', I('get.type'));
        if ($_GET['is_ajax']) {
            return $this->fetch('ajax_order_list');
            exit;
        }
        
        
        return $this->fetch();
    
    }

   
 
}