<?php
/** 
 * 通知chat聊天模块
 */
namespace app\chat\controller;
use app\home\logic\UsersLogic;


use think\Page;
use think\Db;
class Index extends MobileBase {

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
            $this->assign('user_id',$user['user_id']);
        }
    }
    
        /*
         * 聊天首页
         * 
         * */
    public function index(){

    	$a = M('chat')->where('receive','neq',$this->user_id)->where('send',$this->user_id)->field('receive')->distinct(true)->select();
    	$b = M('chat')->where('send','neq',$this->user_id)->where('receive',$this->user_id)->field('send')->distinct(true)->select();
        
      
       //$b = $this->more_array_unique($b);
       // dump($a);
       
        
        //echo '正在开发中';
        
       $count_a = count($a);
       $count_b = count($b);
       
       
       
       if($count_a !==0){
           $a = $this->more_array_unique($a);
           $this->assign('a',$a);
       }
       
       if($count_b !==0){
           $a = $this->more_array_unique($b);
           $this->assign('b',$b);
       }
       
       if($count_a ==0 && $count_b ==0){
           $this->error('你最近没有联系人');
       }
        
      
       
        return $this->fetch();
    }
        
   
        function more_array_unique($arr=array()){  
            foreach($arr[0] as $k => $v){  
                $arr_inner_key[]= $k;   //先把二维数组中的内层数组的键值记录在在一维数组中  
            }  
            foreach ($arr as $k => $v){  
                $v =join(",",$v);    //降维 用implode()也行  
                $temp[$k] =$v;      //保留原来的键值 $temp[]即为不保留原来键值  
            }  
           
            $temp =array_unique($temp);    //去重：去掉重复的字符串  
            foreach ($temp as $k => $v){  
                $a = explode(",",$v);   //拆分后的重组 如：Array( [0] => james [1] => 30 )  
                $arr_after[$k]= array_combine($arr_inner_key,$a);  //将原来的键与值重新合并  
            }  
            //ksort($arr_after);//排序如需要：ksort对数组进行排序(保留原键值key) ,sort为不保留key值  
            return $arr_after;  
        } 
     
        
    public function send(){
        $user_id = session('user_id');
        if($user_id == 0){
                $user_id = I('send');
                if($user_id == 0){
                 $this->error('还没登陆');
                 exit; 
                }
        }
        $send = $user_id;
        $order_id = I('order_id');
        $receive = I('receive');
        
        if(IS_POST){
            $send_role = I('send_role');
            
            $content = I('content');
            $return = $this->msg($receive,$send,$send_role,$content,$order_id);
            $obj = json_decode($return);
            
            $errcode = $obj->{"errcode"};//int 0
            $errmsg = $obj->{"errmsg"};//string 2    ok
            
            $this->save_msg($receive,$send,$send_role,$content,$order_id,$errcode,$errmsg);
            
            if($errcode==0){
                $this->success('发送成功','index');
            }else{
                $this->error('发送失败'.$errmsg,'index');
            }
            
            exit;
            
        }
    
        
        $this->assign('name',get_name_by_uid($receive));
       return $this->fetch();
        
        
    }
        
    public function reply(){
       if($this->user_id == 0){
             $this->error('还没登陆');
             exit;        
        }
        $send = $this->user_id;
        $order_id = I('order_id');
        $receive = I('receive');
        if(IS_POST){
            $send_role = I('send_role');
           
            $content = I('content');
            $return = $this->msg($receive,$send,$send_role,$content,$order_id);
            $obj = json_decode($return);
            
            $errcode = $obj->{"errcode"};//int 0
            $errmsg = $obj->{"errmsg"};//string 2    ok
            
            $this->save_msg($receive,$send,$send_role,$content,$order_id,$errcode,$errmsg);
            
            if($errcode==0){
                $this->success('发送成功','index');
            }else{
                $this->error('发送失败'.$errmsg,'index');
            }
            
            exit;
            
        }
    
        $name = M('users_qiang')->where('user_id',$receive)->getField('name');
        $this->assign('name',$name);
       return $this->fetch();
        
        
    }

    public function save_msg($receive,$send,$send_role,$content,$order_id,$errcode,$errmsg){
        $data['send'] = $send;
        $data['receive'] = $receive;
        $data['send_role'] = $send_role;
        $data['content'] = $content;
        $data['order_id'] = $order_id;
        $data['errcode'] = $errcode;
        $data['errmsg'] = $errmsg;
        M('chat')->save($data);
  
        
    }
        /*
         * 向用户推送消息
         */
    public function msg($receive,$send,$send_role,$content,$order_id){
            $url = 'http://www.yykddn.com/chat/index/reply/receive/'.$send.'/send/'.$receive.'/order_id/'.$order_id.'/send_role/用户';
            $content2 = '<a href="'.$url.'">点这里回复此消息</a>';
            
            if(!$send_role){
                $begin = '用户（'.get_name_by_uid($send).'）对你说：';
            }else {
                $begin = $send_role.'（'.get_name_by_uid($send).'）对你说：';
            }
            
            $content = $begin.'
    
'.$content.'
    
'.$content2;
           
            $openid = M('users') ->where('user_id',$receive)->getField('openid');
            
            
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