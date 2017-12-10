<?php
namespace app\box\controller;
use app\home\logic\UsersLogic;
use app\box\logic\SendLogic;
use Think\Db;
class Send extends MobileBase {
    //根据传来的order_sn，判断是否支付成功
    public function check(){
        $order_id = I('order_id');
        $order = M('kd_order_box')->where("order_id", $order_id)->find();
        if($order['pay_status']==1){
           $this->redirect('chenggong', array('order_id' => $order_id), 1, '支付成功...');
        }
        
    }
    
    public function chenggong() {
        $order_id = I('order_id/d');
        $order = M('kd_order_box')->where("order_id", $order_id)->find();
        $type = I('type');
    
        $errcode = M('msg_box')->where("order_id", $order_id)->getField('errcode');
        if($errcode !== 0){
               $logic = new SendLogic();
               $data = $logic->kd_ok($order_id);
               $data = json_decode($data,true);
               $data = array_merge($data,array("order_id"=>$order_id));
               M('msg_box')->add($data);
        }
       
        $this->assign('type',$type);
        $this->assign('order',$order);
        return $this->fetch();
    }

   
    //微信Jssdk 操作类 用分享朋友圈 JS
    public function ajaxGetWxConfig(){
    	$askUrl = I('askUrl');//分享URL
    	$weixin_config = M('wx_user')->find(); //获取微信配置
    	$jssdk = new \app\mobile\logic\Jssdk($weixin_config['appid'], $weixin_config['appsecret']);
    	$signPackage = $jssdk->GetSignPackage(urldecode($askUrl));
    	if($signPackage){
    		$this->ajaxReturn($signPackage,'JSON');
    	}else{
    		return false;
    	}
    }
 
    
}