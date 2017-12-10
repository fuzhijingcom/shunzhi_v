<?php
namespace app\jijian\controller;
use app\home\logic\UsersLogic;
use app\jijian\logic\SendLogic;
use app\jijian\logic\JijianLogic;
use Think\Db;
class Send extends MobileBase {
	//根据传来的order_sn，判断是否支付成功
	public function check(){
		$order_id = I('order_id');
		$order = M('kd_order_ji')->where("order_id", $order_id)->find();
		if($order['pay_status']==1){
			$this->redirect('paychenggong', array('order_id' => $order_id), 1, '支付成功...');
		}
		
	}
	//付款成功后，执行
	public function paychenggong(){
		$order_id = I('order_id/d');
		$order = M('kd_order_ji')->where("order_id", $order_id)->find();
		
		
		$errcode = M('msg_ji')->where("order_id", $order_id)->getField('errcode');
		if($errcode !== 0){
			$logic = new SendLogic();
			$data = $logic->jijian_pay_ok($order_id);
			$data = json_decode($data,true);
			$data = array_merge($data,array("order_id"=>$order_id));
			M('msg_ji')->add($data);
		}
		
		return $this->fetch();
		
	}
	
    public function chenggong() {
    	
        $order_id = I('order_id/d');
        $order = M('kd_order')->where("order_id", $order_id)->find();
        $type = I('type');
    
        $errcode = M('msg')->where("order_id", $order_id)->getField('errcode');
        if($errcode !== 0){
               $logic = new SendLogic();
               $data = $logic->jijian_ok($order_id);
               $data = json_decode($data,true);
               $data = array_merge($data,array("order_id"=>$order_id));
               M('msg')->add($data);
        }
     
        
        
        $qiang = $order['qiang'];
       
        if($qiang==1){
        	$qiangurl =  "http://www.yykddn.com/kuaidi/send/send_all?order_id=".$order_id;
        	$ch=curl_init();
        	curl_setopt($ch, CURLOPT_URL, $qiangurl);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	$out=curl_exec($ch);
        	curl_close($ch);
        }
        
        
        
        $this->assign('type',$type);
        $this->assign('order',$order);
        return $this->fetch();
    }
    
}