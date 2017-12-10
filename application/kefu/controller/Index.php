<?php
namespace app\kefu\controller;
use app\home\logic\UsersLogic;
use app\kefu\logic\KefuLogic;
use Think\Db;
class Index extends MobileBase {

    public function index(){
        
        $kefu = M('yuangong')->where('grade',8)->select();
        
        $this->assign('kefu',$kefu);
        
        
        return $this->fetch();
    }

    
    public function wenti(){
        $order_id = I('order_id');
        $source = I('source');
        
        if($source=='kd'){
            $model = M('kd_order');
        }
        
        $order = $model->where('order_id',$order_id)->find();
        if(!$order){
            $this->error('订单不存在');
        }
        
        $this->assign('order',$order);
        
        return $this->fetch();
    }
    
    
    public function bug(){
    
    echo '正在开发';
       // return $this->fetch();
    }
    
   public function guoqi(){
       
       
       //$date = '2017-08';
       $order_status = 0;
       
      // $condition['add_time'] = array('like','%'.$date.'%');
       $condition['pay_status'] = array('eq',1);

       $condition['order_id'] = array('lt',6500);
        

       $condition['order_status'] = array('eq',$order_status);
       
       $result = D('kd_order')->where($condition)->field('order_id')->order('order_id desc')->select();
       
       $c = count($result);
       
       dump($c);
       
      
      
       echo '<h1>6000:96</h1>';
       echo '<h1>6500:148</h1>';
       echo '<h1>6500:112</h1>';
       echo '<h1>6500:105</h1>';
       
       dump($result);
       exit;
       
       $logic = new KefuLogic();
       
       //$data = $logic->push_msg_guoqi(2);
       
       
       
       for ($i = 0; $i < $c; $i++){
           $order_id =  $result[$i]['order_id'];
           $user_id =  $result[$i]['user_id'];
           
               $data = $logic->push_msg_guoqi($order_id);
               $data = json_decode($data,true);
               
               $data = array_merge($data,array("order_id"=>$order_id));
               M('msg_guoqi')->save($data);
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