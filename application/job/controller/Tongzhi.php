<?php
/** 
 * 通知
 */
namespace app\job\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;

use think\Page;
use think\Db;
class Tongzhi extends MobileBase {

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
        
        
     $o = $this->daoda();
     $obj = json_decode($o);
     
     
    $errcode = $obj->{"errcode"};
    $msgid = $obj->{"msgid"};
    $errmsg = $obj->{"errmsg"};
    
    
    
    
    dump($errcode);
    dump($msgid);
    dump($errmsg);

    }
    
    //校园快件通知
    public function daoda(){
    
        $type = '韵达快递';
        
        
            $kd_order_model = M('kd_order');
            $order_sn = $kd_order_model ->where('order_id',$order_id)->getField('order_sn');
        
            $user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
            $receiver = $kd_order_model ->where('order_id',$order_id)->getField('receiver');
             
            $openid = M('users') ->where('user_id','1')->getField('openid');
            $id =  M('users_kd') ->where('user_id',$receiver)->getField('user_id');
            $name =  M('users_kd') ->where('user_id',$receiver)->getField('name');
            $mobile =  M('users_kd') ->where('user_id',$receiver)->getField('mobile');
        
           
            $token_url = 'http://yy.yudw.com/home/conf';
            $access_token = file_get_contents($token_url,true);;
            $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            $json = array(
                'touser'=> $openid,
                'template_id'=>"zEeOZxDW1D0wcB4WuV7ZqtHpEDb8iyioQGeoZhtNugk",
                'url'=>"http://www.yykddn.com/mobile/kuaidi/",
                'data'=>array(
                    'first'=>array(
                        'value'=> "你的快递已到达
",
                        'color'=>"#0000CD"
                    ),
                    'keyword1'=>array(
                        'value'=> $type,
                        'color'=>"#000000"
                    ),
                    'keyword2'=>array(
                        'value'=> '广东轻工职业技术学院（南海）',
                        'color'=>"#000000"
                    ),
                    'keyword3'=>array(
                        'value'=> '15813439851',
                        'color'=>"#000000"
                    ),
                    'keyword4'=>array(
                        'value'=> '3号门',
                        'color'=>"#000000"
                    ),
        
                    'remark'=>array(
                        'value'=>"取件时间：11点半
        
点击  详情 ，下单代拿",
                        'color'=>"#000000"
                    )
                )
            );
        
            $json = json_encode($json);
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $out = curl_exec($ch);
            curl_close($ch);
            
            
          return $out;
            
    }
    public function chat(){
        if($this->user_id == 0){
            header("Location: " . "http://www.yykddn.com/codetoany/getcode.php?auk=chat" );
        }
        
        //$kuaidi = M('kd')->where('status','1')->select();
         
        //dump($kuaidi);
        
        $this->assign('kuaidi', $kuaidi );
        //echo '这里是快递服务';
        
        return $this->fetch();
        
    
    }
    
    public function send(){

        if($this->user_id == 0){
           $this->error('还没登陆');
            exit;        
        }
    
        //$kuaidi = M('kd')->where('status','1')->select();
         
        //dump($kuaidi);
    if(IS_POST){
        
        if(I('id')==null && I('mobile')==null){
            $this->error('ID或手机号码 必须 要填写一个');
        }
        if(I('content')==null){
            $this->error('内容是空的');
        }
        
       $id = I('id');
        if(!empty($id)){
            $id = I('id');
        }else {
            $mobile = I('mobile');
            $id = M('user_address') ->where('mobile',$mobile)->getField('user_id');
        }

        $content = I('content');
        $return = $this->msg($id,$content,$this->user_id);
        $obj = json_decode($return);
        
        $errcode = $obj->{"errcode"};
        $errmsg = $obj->{"errmsg"};
        
        
        header("Location: " . "http://www.yykddn.com/job/tongzhi/ok/errcode/".$errcode."/errmsg/".$errmsg );
        exit;
        
    }

    
    
        return $this->fetch();
    
    
    }
    
    public function reply(){
        $from = I('from');
        if($this->user_id == 0){
            $this->error('还没登陆');
            exit;
        }
    
        if(IS_POST){
            $id = $from;
            if(I('content')==null){
                $this->error('内容是空的');
            }

            $content = I('content');
            $return = $this->msg($id,$content,$this->user_id);
            $obj = json_decode($return);
        
            $errcode = $obj->{"errcode"};
            $errmsg = $obj->{"errmsg"};
        
            header("Location: " . "http://www.yykddn.com/job/tongzhi/ok/errcode/".$errcode."/errmsg/".$errmsg );
            exit;
        
        }
        
        
         
        $this->assign('from', $from);
        return $this->fetch();
    
    
    }
    public function ok(){
        $errcode = I('errcode');
        $errmsg = I('errmsg');
        if($errcode==0){
            $this->assign('status', '发送成功' );
        }else{
            $this->assign('status', '发送失败' );
        }
        $this->assign('errcode', $errcode);
        $this->assign('errmsg', $errmsg);
        return $this->fetch();
    }
    /*
     * 向用户推送消息
     */
    public function msg($id,$content,$from){
        $url = 'http://www.yykddn.com/job/tongzhi/reply/from/'.$from;
        $content2 = '<a href="'.$url.'">点这里回复此消息</a>';
        
        if($from == 1){
            $begin = '驿源：';
        }else {
            $begin = '用户'.$from.'对你说：';
        }
        
        $content = $begin.'

'.$content.'

'.$content2;
       
        $openid = M('users') ->where('user_id',$id)->getField('openid');
        
        
        $access_token = access_token();
        
        $url ="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        $json = array(
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>array(
                'content'=>$content,
            )
        );
        
        header("Content-type: text/html; charset=utf-8");
       
        
        
        $json = json_encode($json,JSON_UNESCAPED_UNICODE);
        
      
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
        return $out;
        
        
    }
    
   
   
}