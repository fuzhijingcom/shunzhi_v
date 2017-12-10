<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;
use app\work\logic\YuangongLogic;

use think\Page;
use think\Db;
class Sign extends MobileBase {

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
        $grade =  M('yuangong') ->where("yid",$this->user_id)->getField('grade');     
        if(!$grade || $grade < 1  ){
            $this->redirect(U("work/error/noauth"));
            exit;
        }
        
    }
   
    public function index(){
        //$date = date('2017-09-12');
        $date = date('Y-m-d');
        $condition['receivetime'] = array('like','%'.$date.'%');
        
       
       
       $d = M('kd_order')->field("receiver,count(*) as count")->where($condition)->group("receiver")->where('order_status',1)->select();
       // 
       //dump($d);
        
       $count_d  = count($d);
       
      // dump($count_d);
       
       $hour = (int)date('H');
       if($hour == 23 || $hour == 22 || $hour == 21 || $hour == 20  ){
       
           

               for ($i = 0; $i < $count_d; $i++){
                   $id = $d[$i]['receiver'];
                   $data['count'] =  $d[$i]['count'];
               
                  
                   $data['id'] = $id;
                   $sendcode = M('work_sign_count')->where(array("id"=>$id,"date"=>$date ))->find();
                   
                   if(!$sendcode){
                       $data = array_merge($data,array("date"=>$date,"send"=>0));
                       M('work_sign_count')->save($data);
                   }
                   
                   
               }
       
       
       }
       
       
       
       
       
        
        $count = M('work_sign_count')->order('date desc,count desc')->select();
        $this->assign('count',$count);
        $work_sign_count = M('work_sign_count')->where(array('send' => 0 ))->select();
        $c = count($work_sign_count);   
        $logic = new YuangongLogic();
        
        for ($i = 0; $i < $c; $i++){
            $id =  $work_sign_count[$i]['id'];
            $openid = M('users')->where('user_id',$id)->getField('openid');
        
            $sendcode = M('work_sign_count')->where(array("id"=>$id,"send"=>0 ))->getField('send');
            if($sendcode !== 1){
                $data = $logic->push_msg_count($openid,'揽件',$work_sign_count[$i]['date'],'3号门','是',$work_sign_count[$i]['count']);
                $data = json_decode($data,true);
                //$data = array_merge($data,array("order_id"=>$order_id,"openid"=>$openid));
               M('work_sign_count')->where(array("id"=>$id))->save(array("send"=>1));
            }
        }
        
        
        
        
        return $this->fetch();
    }
    public function daily_list(){
        $user_id = session('user.user_id');
        if(!$user_id){
            $this->error('还未登录');
        }
        
        $list = M('work_daily')->where('id',$user_id)->select();
        $this->assign('list',$list);
        
        return $this->fetch();
        
        
    }
    
    public function daily_add(){
        $user_id = session('user.user_id');
        if(!$user_id){
            $this->error('还未登录');
        }
        
        $count_id = I('count_id');
        $has_write = M('work_daily')->where('count_id',$count_id)->find();
        if($has_write){
            $this->error('已经填写了','index');
        }
        if(!$count_id){
            $this->error('入口出错','index');
        }
        
        $zhuguan = M('yuangong')->where('grade',7)->select();
        $this->assign('zhuguan',$zhuguan);
       
        
        if(IS_POST){
           
            $data['count_id'] = $count_id;
            $data['id'] = $user_id;
            $data['daily'] = I('daily');
            if(!$data['daily']){
                $this->error('今日工作总结不能为空');
            }
            $data['learn'] = I('learn');
            $data['zhuguan'] = I('zhuguan');
            M('work_daily')->add($data);
            $this->redirect('daily_list');
        }
        
        
        return $this->fetch();
    }
    
    
   
}