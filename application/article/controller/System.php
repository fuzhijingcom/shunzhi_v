<?php
namespace app\article\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class System extends MobileBase {

    public function index(){
      $uid = session('user.user_id');
        if($uid){
          $time = M('article_msg')->where(array('uid'=>$uid,'article_id'=>1))->order('id desc')->getField('time');
          $time = substr($time,0,10);
          
           
          if($time !== date('Y-m-d')){  
            
                   $content = '请问您对我们系统由兴趣吗？';
                   
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
                    $data['article_id'] = 1;
                    
                    M('article_msg')->data($data)->save();
          }
            
        }
        
        $this->assign('title','驿源科技简介');
        $this->assign('url','http://www.yykddn.com/article/system?share_id='.$user_id);
        $this->assign('img','http://www.yykddn.com/public/job/new.jpg');
     
        $this->assign('desc','驿源系统简介');
        
        
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