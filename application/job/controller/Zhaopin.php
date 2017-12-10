<?php
namespace app\job\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Zhaopin extends MobileBase {

    public function index(){
      $uid = session('user.user_id');
        if($uid){
          $time = M('job_msg')->where('uid',$uid)->order('id desc')->getField('time');
          $time = substr($time,0,10);
          
           
          if($time !== date('Y-m-d')){  
            
                   $content = '期待您的加入，点下面，撩我吧';
                   
                   $url = "http://www.yykddn.com/job/tongzhi/msg/id/".$uid."/content/".$content.'/from/1' ;
                   
                   $ch=curl_init();
                   curl_setopt($ch, CURLOPT_URL, $url);
                   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                   curl_setopt($ch, CURLOPT_POST, 1);
                   curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                   $out=curl_exec($ch);
                   curl_close($ch);
        
                   
                   
                    $obj = json_decode($out);
                    
                    $errcode = $obj->{"errcode"};
                    $errmsg = $obj->{"errmsg"};
                    
                    $data['errcode'] = $errcode;
                    $data['errmsg'] = $errmsg;
                    $data['uid'] = $uid;
                    $data['share_id'] = I('share_id');
                    
                    M('job_msg')->data($data)->save();
          }
            
        }
        
        $this->assign('title','驿源，有属于你的位置！ 够胆你就来！');
        
        $nickname = session('user.nickname');
        $this->assign('desc','我是'.$nickname.'，驿源有你想要的一切！只要敢想，没有什么做不了！ 我们的成长邀请你一同参与！');
        
        
        $this->assign('user_id',$uid);
        
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