<?php
namespace app\article\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Detail extends MobileBase {

    public function index(){
        $uid = session('user.user_id');
         
        $id = I('id/d');
        if(!id){
            $this->error('文章ID错误');
            exit;
        }
    
        $article = M('article')->where('article_id',$id)->find();
        $this->assign('article',$article);
         
    
        $this->assign('title',$article['title']);
    
        $nickname = session('user.nickname');
        $this->assign('desc','驿源');
        $this->assign('url','http://www.yykddn.com/job/jianzhi?id='.$id);
        $this->assign('images','http://www.yykddn.com/public/job/jianzhi.jpg');
    
        $this->assign('user_id',$uid);
    
        return $this->fetch();
    }
    
    
    
    public function uid(){
        $uid = session('user.user_id');
        echo '<h1>'.$uid.'</h1>';
    }
    
    public function zhuanchaben(){
        
        echo 'stop';
        exit;
        
        
      $uid = session('user.user_id');
        if($uid){
          $time = M('article_msg_zhuanchaben')->where('uid',$uid)->order('id desc')->getField('time');
         // $time = substr($time,0,10);
          
           
          if(!$time){  
            
                   $content = '2018专插本，机不可失，快咨询获取优惠吧';
                   
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
                    
                    M('article_msg_zhuanchaben')->data($data)->save();
          }
            
        }
        
        
        
        $all = M('article_msg_zhuanchaben')->field('uid')->select();
        $this->assign('all',$all);
       
        $this->assign('title','大专生的你，如何备考18年的专插本？');
        $this->assign('url','http://www.yykddn.com/article/detail/zhuanchaben?share_id='.$uid);
        $this->assign('img','http://www.yykddn.com/public/article/zhuanchaben.jpg');
     
        $this->assign('desc','这套资料涉及到考试大纲，重点笔记，历年真题，你值得拥有哦。');

        return $this->fetch();
    }

    public function xueche(){
        $uid = session('user.user_id');
        if($uid){
            $time = M('article_msg')->where(array('uid'=>$uid,'article_id'=>2))->order('id desc')->getField('time');
            // $time = substr($time,0,10);
    
             
            if(!$time){
    
                $content = '来吧，老司机带你飞？好不好';
                 
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
                $data['article_id'] = 2;
                M('article_msg')->data($data)->save();
            }
    
        }
    
    
    
        $all = M('article_msg')->where('article_id',2)->field('uid')->select();
        $this->assign('all',$all);
         
        $this->assign('title','来吧。老司机带你飞');
        $this->assign('url','http://www.yykddn.com/article/detail/xueche?share_id='.$uid);
        $this->assign('img','http://www.yykddn.com/public/article/xueche.jpg');
         
        $this->assign('desc','学车优惠');

        return $this->fetch();
    }
    
    public function jijian(){
        $uid = session('user.user_id');
       
        $this->assign('title','最优惠的寄件：满五次减五元、一元一斤');
        $this->assign('url','http://www.yykddn.com/article/detail/jijian?share_id='.$uid);
        $this->assign('img','http://www.yykddn.com/public/upload/ad/jijian.jpg');
         
        $this->assign('desc','最优惠的寄件，一饭三门口店面。寄件优惠：满五次减五元、一元一斤');
    
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