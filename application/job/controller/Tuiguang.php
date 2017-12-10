<?php
namespace app\job\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;

use think\Page;
use think\Db;
class Tuiguang extends MobileBase {

    public $user_id = 0;
    public $user = array();
    /**
     * 析构流函数
    */
    public function  __construct() {
        parent::__construct();
       // $this->cartLogic = new \app\home\logic\CartLogic();
        if(session('?user'))
        {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user',$user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user',$user); //存储用户信息
           
        }
    }
    
    
    public function index(){
        $article_id = I('article_id');
        $share_id = I('share_id/d');
        if(session('article_id') !== null && $article_id == null){
            $article_id = session('article_id');
            $share_id = session('share_id');
            session('article_id', null);
            session('share_id', null);
            echo '<script type="text/javascript">window.location.href=\'' . 'http://www.yykddn.com/mobile/tuiguang/index/article_id/'.$article_id.'/share_id/'.$uid.'\';</script>';
            
            exit;
        }
        $uid = session('user.user_id');
        if ($uid == null && is_weixin() == true) {
            //未登录
            session('article_id', $article_id);
            session('share_id', $share_id);
            header("Location: " . "http://www.yykddn.com/codetoany/getcode.php?auk=tg" );
            exit;
        }
        if ($uid == null && is_weixin() == false) {
            header("Location: " . "http://www.yykddn.com/mobile" );
            exit;
        }
        $username = session('user.nickname');
        $reader_openid = session('user.openid');
       
        //dump($reader_openid);
    
        $userModel = M('users');
        $head_pic = $userModel->where("user_id",$uid)->getfield('head_pic');
        $this->assign('head_pic',$head_pic);
       
        
        
        if($article_id=='0' || $article_id==null){
            $this->error('没有获取到文章信息');
        }
        if($share_id=='0'){
        $this->error('没有获取到分享者信息');
        }
        
        
        
        
        //获取文章点击信息
        $clickModel = M('tuiguang_click');
        $read_openid = session('user.openid');
        $condition['reader_openid'] = $read_openid;
   
        $click = $clickModel->where($condition)->where('share_id',$share_id)->count('id');
        
        if($click==0){
           
            $data = array(
                'share_id' => $share_id,
               'reader_openid' => $reader_openid,
                'article_id' => $article_id,
                'reader_img' => session('user.head_pic'),
                
                );
           $clickModel->insert($data);
           $this->assign('help','1');
        }
       
        
        //$this->assign('article',$article);
        
        
        
        if($share_id !== $uid){
            
            echo '<script type="text/javascript">window.location.href=\'' . 'http://www.yykddn.com/mobile/tuiguang/index/article_id/'.$article_id.'/share_id/'.$uid.'\';</script>';
            
           exit;
        }
        
        
       
        //获取文章内容
        $articleModel = M('tuiguang_article');
        $article = $articleModel->where("article_id",$article_id)->find();
        $this->assign('article',$article);
        
        $max = $article['max'];
        $this->assign('max',$max);
        
        //获取访客
        $fangkeModel = M('tuiguang_click');
        $con['share_id'] = $uid;
        $fangke = $fangkeModel->where($con)->where("article_id",$article_id)->select();
        
        $fangke_num = $fangkeModel->where($con)->where("article_id",$article_id)->count();
        
        $this->assign('fangke_num',$fangke_num);
        $this->assign('fangke',$fangke);
        
        $Jssdk = new \app\mobile\logic\Jssdk();
        $signPackage = $Jssdk->getSignPackage();

        $this->assign('signPackage',$signPackage);
        
         return $this->fetch();
    
    }
    
    
 
   
    public function ajaxGetMore(){
    	$p = I('p/d',1);
    	$favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1")->order('goods_id DESC')->page($p,10)->cache(true,TPSHOP_CACHE_TIME)->select();//首页推荐商品
    	$this->assign('favourite_goods',$favourite_goods);
    	return $this->fetch();
    }
}