<?php
namespace app\mobile\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\SendLogic;
use Think\Db;
class Send extends MobileBase {

    public function index(){
       
       // return $this->fetch();
    }

    public function order(){
        $user_id = session('user.user_id');
        if(!$user_id){
            $this->error('登录已失效');
        }
        
        
         $order_id = I('id');
         
         $order = M('order')->where(array('order_id'=>$order_id))->find();
         
         if(!$order){
             $this->error('订单不存在');
         }
        
        if($order['pay_status'] !== 1 ){
            $this->error('订单未支付');
        }
        
       
       
        $order_sn = $order['order_sn'];
        $name = $order['consignee'];
        $mobile = $order['mobile'];
        $sushe = $order['sushe'];
        $user_id = $order['user_id'];
        $order_amount = $order['order_amount'];
        
        
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        
        $good = M('order_goods')->where('order_id',$order_id)->find();
        $goods_name = $good['goods_name'];
        $goods_num = $good['goods_num'];
        
        
        
        //通知下单人
        $logic = new SendLogic();
        $data = $logic->ok($order_id,$openid,$order_sn,$name,$mobile,$sushe,$goods_name,$goods_num);
        $data = json_decode($data,true);
        
        
        
        
        $logic->new_order($order_id,$openid,$order_sn,$name,$mobile,$sushe,$goods_name,$goods_num,$order_amount);
        
        
        
        $this->redirect('Mobile/User/order_detail', array('id' => $order['order_id']), 1, '页面跳转中...');
      
        // return $this->fetch();
    }
       
}