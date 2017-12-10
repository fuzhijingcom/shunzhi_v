<?php
namespace app\job\controller;
use app\home\logic\UsersLogic;
use Think\Db;
class Jianzhi extends MobileBase {

    public function index(){
      $uid = session('user.user_id');
       
      $id = I('id/d');
      if(!id){
          $this->error('文章ID错误');
          exit;
      }
      
      $article = M('article')->where('article_id',$id)->find();
      $this->assign('article',$article);
     
      if(!empty($article['deadline']) && ( time() > $article['deadline'])) {
          
          $this->error('兼职已经过期','joblist');
          exit;
      }
      
        $this->assign('title',$article['title']);
        
        $nickname = session('user.nickname');
        $this->assign('desc','驿源兼职，值得信赖！');
        $this->assign('url','http://www.yykddn.com/job/jianzhi?id='.$id);
        $this->assign('images','http://www.yykddn.com/public/job/jianzhi.jpg');
        
        $this->assign('user_id',$uid);
        
        return $this->fetch();
    }

    public function joblist(){
        $list = M('article')->where(array('cat_id'=>93,'is_open'=>1))->order('article_id desc')->select();
        
        $this->assign('list',$list);
        
        
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