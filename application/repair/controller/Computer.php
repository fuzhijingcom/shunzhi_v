<?php
namespace app\repair\controller;

use app\home\logic\UsersLogic;
use app\home\model\Message;
use app\common\logic\OrderLogic;
use think\Page;
use think\Request;
use think\Verify;
use think\db;

class Computer extends MobileBase
{

    public $user_id = 0;
    public $user = array();

    /*
    * 初始化操作
    */
    public function _initialize()
    {
        parent::_initialize();
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
        }
       
    }

    /*
     * 用户中心首页
     */
    public function index()
    {
        $user_id = session('user.user_id');
        
        $address = M('user_address')->where(['user_id'=>$user_id,'is_default'=>1])->find();
        
        
       if(IS_POST){
           $data['name'] = $name= session('user.nickname');
           $data['mobile'] = $mobile = I('mobile');
           $data['problem'] = $problem = I('problem');
           $data['user_id'] = $user_id;
           
           
           M('repair')->data($data)->save();
           
           
           $content = '【电脑维修】名字：'.$name.'，联系方式：'.$mobile.'，问题：'.$problem;
           $this->send($content);
           $this->send1($content);
           $this->success('提交成功','index');
       }
       
       $this->assign('address', $address);
       return $this->fetch();
    }

    
    private function send($content)
    {
       
        $user_id = session('user.user_id');
        $url = "http://www.yykddn.com/chat/index/send/receive/9687/send/".$user_id."/content/".$content;
        $json = array(
        );
        
        $json = json_encode($json);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
        
       // dump($json);
    }
    
    
    private function send1($content)
    {
    	
    	$user_id = session('user.user_id');
    	$url = "http://www.yykddn.com/chat/index/send/receive/1/send/".$user_id."/content/".$content;
    	$json = array(
    	);
    	
    	$json = json_encode($json);
    	$ch=curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	$out=curl_exec($ch);
    	curl_close($ch);
    	
    	// dump($json);
    }
    
}

