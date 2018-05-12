<?php
namespace app\kuaidi\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Index extends MobileBase {

	
    public function index(){
		
		$openid_lb = session('user.openid_lb');
    	//去获取一下openid_bx
    	if($openid_lb == NULL &&  I('oid') == NULL){
    		$url  = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $url = urlencode($url);
            $url = 'http://c3w.cc/entry/click?ReturnUrl='.$url;
    		//$url = 'http://www.yykddn.com/api/login?url='.$url;
    		header("Location:".$url);
    		exit;
		}
		
		$get_openid_lb = I("oid");
		
		if($openid_lb == NULL &&  $get_openid_lb  ){
    		
    		session('user.openid_lb',$get_openid_lb);
			$user_id= session('user.user_id');
			
			M('users')->where('user_id',$user_id)->save(array('openid_lb'=>$get_openid_lb));
			
		}

		$openid_lb = session('user.openid_lb');
		if(!$openid_lb){
			$this->error("新openid出错");
			exit;
		}
		//获取结束
		


    	// $openid_yy = session('user.openid_yy');
    	// //去获取一下openid_yy
    	// if($openid_yy == NULL &&  I('openid') == NULL){
    	// 	$url  = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    	// 	$url = urlencode($url);
    	// 	$url = 'http://www.yykddn.com/api/login?url='.$url;
    	// 	header("Location:".$url);
    	// 	exit;
    	// }else{
    	// 	$openid_yy = I("openid");
    	// 	session('user.openid_yy',$openid_yy);
    	// 	$user_id= session('user.user_id');
    	// 	M('users')->where('user_id',$user_id)->save(array('openid_yy'=>$openid_yy));
    	// }
    	// //获取结束
		
			//通知查漏补缺
			$url =  "http://v.yykddn.com/kuaidi/send/checksend";
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$out=curl_exec($ch);
			curl_close($ch);
		

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