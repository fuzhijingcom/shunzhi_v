<?php
namespace app\kuaidi\controller;
use app\home\logic\UsersLogic;
use app\kuaidi\logic\SendLogic;
use Think\Db;
class Send extends MobileBase {
    //根据传来的order_sn，判断是否支付成功
    public function check(){
        $order_id = I('order_id');
       
        //去获取
        $pay_status= M("kd_order")->where("order_id",$order_id)->getField('pay_status');
        
        //只有当未付款时，才去获取订单数据
        if($pay_status == 0){
		        $url = "http://www.yykddn.com/pay/payment/payresult?order_id=".$order_id;
		        $ch = curl_init();
		        curl_setopt($ch, CURLOPT_URL, $url);
		        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		        $out = curl_exec($ch);
		        curl_close($ch);
		        
		        $order = json_decode($out,true);
		        
		        if(!$order){
		        	$this->error("支付出错");
		        	exit;
		        }
		        
		        $order_id = $order['order_id'];
		        
		        $cunzai = M("kd_order")->where("order_id",$order_id)->find();
		        if(!$cunzai){
		        	M("kd_order")->data($order)->save();
		        }else {
		        	M("kd_order")->where(array("order_id"=>$order_id))->data($order)->save();
		        }
        
        }      
		
        if($pay_status == 1){
           $this->redirect('chenggong', array('order_id' => $order_id), 1, '支付成功...');
        }
        
    }
    
    public function chenggong() {
        $order_id = I('order_id/d');
        $order = M('kd_order')->where("order_id", $order_id)->find();
        $type = I('type');
    
        $errcode = M('msg')->where("order_id", $order_id)->getField('errcode');
        if($errcode !== 0){
               $logic = new SendLogic();
               $data = $logic->kd_ok($order_id);
               $data = json_decode($data,true);
               $data = array_merge($data,array("order_id"=>$order_id));
               M('msg')->add($data);
        }
       
        
        $qiang = $order['qiang'];
        if($qiang==1){
            $qiangurl =  "http://v.yykddn.com/kuaidi/send/send_all?order_id=".$order_id;
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
    
    public function checksend(){

        $ids = M('kd_order')->where(array('pay_status'=>1,'order_status'=>0,'qiang'=>1))->field('order_id,consignee')->order('order_id desc')->limit(10)->select();

        $c = count($ids);
        dump($ids);
        echo $c;
        for($i=0;$i<$c;$i++){

            $order_id =  $ids[$i]['order_id'];
            $qiangurl =  "http://v.yykddn.com/kuaidi/send/send_all?order_id=".$order_id;
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $qiangurl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $out=curl_exec($ch);
            curl_close($ch);
        }
            


    }
     
    public function send_all() {
       
        $order_id = I('order_id');
        $order = M('kd_order')->where("order_id", $order_id)->find();
        $name = $order['consignee'];
        $sushe = $order['sushe'];
        $kuaidi_name = $order['kuaidi_name'];
        $discount = $order['discount'];
        $school = $order['school'];
        $lou = $order['lou'];
        $sex = $order['sex'];
        if($sex == 'sex'){
        	$sex = "不限男女";
        }
        $type = $order['type'];
        //,'school'=>$school 
        
    //,'qun'=>1,'school'=>$school

    $user = M('users_qiang')->where(array('is_validated'=>1,'tuisong'=>1 ))->order('credit desc')->field('openid')->select();
    $c = count($user);
            
            $logic = new SendLogic();
 
           for ($i = 0; $i < $c; $i++){
                $openid =  $user[$i]['openid'];
            
                $errcode = M('msg_qiang')->where(array("order_id"=>$order_id,"openid"=>$openid ))->getField('errcode');
                if($errcode !== 0){
                	$data = $logic->push_msg_all($openid,$order_id,$name,$sushe,$kuaidi_name,$discount,$lou,$sex,$type);
                    $data = json_decode($data,true);
                   
                    $data = array_merge($data,array("order_id"=>$order_id,"openid"=>$openid));
                    M('msg_qiang')->add($data);
                } 
            }
            
    }
     
    public function send_all_second() {
         
        $order_id = I('order_id');
        $order = M('kd_order')->where("order_id", $order_id)->find();
        $name = $order['consignee'];
        $sushe = $order['sushe'];
        $kuaidi_name = $order['kuaidi_name'];
        $discount = $order['discount'];
        $school = $order['school'];
        $lou = $order['lou'];
        $sex = $order['sex'];
        if($sex == 'sex'){
        	$sex = "不限男女";
        }
        $type = $order['type'];

    
        $user = M('users_qiang')->where(array('is_validated'=>1,'tuisong'=>1,'qun'=>1,'school'=>$school ))->field('openid')->select();
        $c = count($user);
    
    
        $logic = new SendLogic();
    
        for ($i = 0; $i < $c; $i++){
            $openid =  $user[$i]['openid'];
    
            $errcode = M('msg_qiang2')->where(array("order_id"=>$order_id,"openid"=>$openid ))->getField('errcode');
            if($errcode !== 0){

                $data = $logic->push_msg_all($openid,$order_id,$name,$sushe,$kuaidi_name,$discount,$lou,$sex,$type);
                $data = json_decode($data,true);
                   
                $data = array_merge($data,array("order_id"=>$order_id,"openid"=>$openid));
                M('msg_qiang2')->add($data);
            }
        }
    
        
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