<?php
namespace app\api\controller;
//use app\home\logic\UsersLogic;
use think\Controller;
use think\Session;

class Userdata extends Controller {
    public function _initialize() {
        Session::start();
        header("Cache-control: private");  // history.back返回后输入框值丢失问题 参考文章 http://www.tp-shop.cn/article_id_1465.html  http://blog.csdn.net/qinchaoguang123456/article/details/29852881
        $this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
    
        $this->weixin_config = M('wx_user')->find(); //获取微信配置
    }
    
    public function index() {
        $token = $this->get_access_token();
        
        echo $token;
        
        //$this->public_assign();
    }
    
    public function getSushe(){
        $key = I('key');
        $msgid = I('msgid');
        $time = date('Y-m-d H:i:s');
        $openid = I('openid');
        
         
        
        
        $sushe = M('kd_order')->where('consignee',$key)->getField('sushe');
        if(!$sushe){
            $sushe = M('kd_order1')->where('consignee',$key)->getField('sushe');
        }
        
        if(!$sushe){
            $sushe = M('user_address')->where('consignee',$key)->getField('sushe');
        }
        
        if(!$sushe){
            $sushe = M('wm_faddress')->where('name',$key)->getField('address');
        }
        
        
        
        if(!$sushe){
            $sushe = '【没找到】';
        }
        
        
        if($this->permission($openid,$key,$time,$sushe,'ss')==false){
            return '【对不起，接口只对内部开放，你没有权限查询】';
        }
        
        
        return $sushe;
        
        
    }
    
    public function getmobile(){
        $key = I('key');
        $msgid = I('msgid');
        $time = date('Y-m-d H:i:s');
        $openid = I('openid');
         
    
        $mobile = M('kd_order')->where('consignee',$key)->getField('mobile');
        if(!$mobile){
            $mobile = M('kd_order1')->where('consignee',$key)->getField('mobile');
        }
    
        if(!$mobile){
            $mobile = M('user_address')->where('consignee',$key)->getField('mobile');
        }
    
        if(!$mobile){
            $mobile = M('wm_faddress')->where('name',$key)->getField('tel');
        }
    
        if(!$mobile){
            $mobile = '【没找到】';
        }

        if($this->permission($openid,$key,$time,$mobile,'hm')==false){
            return '【对不起，接口只对内部开放，你没有权限查询】';
        }
        
        return $mobile;
    
    
    }

    public function getduanhao(){
        $key = I('key');
        $msgid = I('msgid');
        $time = date('Y-m-d H:i:s');
        $openid = I('openid');
        
    
        $duanhao = M('kd_order')->where('consignee',$key)->getField('duanhao');
        if(!$duanhao){
            $duanhao = M('kd_order1')->where('consignee',$key)->getField('duanhao');
        }
    
        if(!$duanhao){
            $duanhao = M('user_address')->where('consignee',$key)->getField('duanhao');
        }
    
        if(!$duanhao){
            $duanhao = '【没找到】';
        }

        if($this->permission($openid,$key,$time,$duanhao,'dh')==false){
            return '【对不起，接口只对内部开放，你没有权限查询】';
        }
        
        return $duanhao;
    
    
    }
    
    private function permission($openid,$key,$time,$value,$type){
        $user_id = M('users')->where('openid',$openid)->getField('user_id');
        $grade = M('yuangong')->where('yid',$user_id)->getField('grade');
        
        $data['user_id'] = $user_id;
        $data['grade'] = $grade;
        $data['keyword'] = $key;
        $data['time'] = $time;
        $data['value'] = $value;
        $data['type'] = $type;
        
        M('inquiry')->save($data);
        
        
        if($grade >= 1 ){
            return true;
        }else{
            return false;
        }
  
    }
    
   
}