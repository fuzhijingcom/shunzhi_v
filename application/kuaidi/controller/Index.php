<?php
namespace app\kuaidi\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Index extends MobileBase {

	
    public function index(){
    	$openid_yy = session('user.openid_yy');
    	//去获取一下openid_yy
    	if($openid_yy == NULL &&  I('openid') == NULL){
    		$url  = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    		$url = urlencode($url);
    		$url = 'http://www.yykddn.com/api/login?url='.$url;
    		header("Location:".$url);
    		exit;
    	}else{
    		$openid_yy = I("openid");
    		session('user.openid_yy',$openid_yy);
    		$user_id= session('user.user_id');
    		M('users')->where('user_id',$user_id)->save(array('openid_yy'=>$openid_yy));
    	}
    	//获取结束
    	
    	
    	$user_id  =  session('user.user_id');
    	
    	$kuaidi = M('kd')->where('status','1')->order('shunxu')->select();
    	$this->assign('kuaidi', $kuaidi );

    	return $this->fetch();
    }

    public function errortime(){
        $errmsg = I('errmsg');
        if(!$errmsg){
        $errmsg = '当前时间段暂停下单<br>下单时间：00:00——13:00<br>15:00——18:00';
        }
        $this->assign('errmsg', $errmsg );
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