<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;
use app\work\logic\YuangongLogic;
use app\work\logic\ShenheLogic;
use think\Page;
use think\Db;
class Qdy extends MobileBase {

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
        if(!$grade || $grade < 6  ){
            $this->redirect(U("work/error/noauth"));
            exit;
        }
        
    }
   
    public function index(){
    	
    	if(IS_POST){
    		$searchrealname = I('searchrealname');
    		$condition['name'] = array('like','%'.$searchrealname.'%');
    	}
    	
    	$this->assign('searchrealname', $searchrealname);
    	
    	$condition['is_validated'] = array('eq',1);
    	$all = M('users_qiang')->where($condition)->select();
    	$this->assign('all',$all);
    	return $this->fetch();
    }
    
    
    public function action(){
        $id = I('id');
        $is_validated = I('is_validated');
        $reason = I('reason');
        
        M('users_qiang')->where(array('user_id'=>$id))->save(array('is_validated'=>$is_validated));
    
        //发送审核通过通知
        
        $logic = new ShenheLogic();
        $data = $logic->shenhe($is_validated,$id,$reason);
       
        
        $this->redirect(U("work/shenhe/index"));
    }
    
    public function refuse(){
        $id = I('id');
        
        if(IS_POST){
            $reason = I('reason');
            $url = '/work/shenhe/action/?is_validated=3&id='.$id.'&reason='.$reason;
            $this->redirect($url);
        }
        
        /*
         * 
         * function refuse(id){
            	if(confirm("拒绝该用户?")){
            	location.href='/work/shenhe/action/?is_validated=3&id='+id;
            	}	　　
            }
         * 
         * 
         * */
        
        
        return $this->fetch();
    }
    
   
    
    
    public function order(){
    	$user_id = I('user_id');
    	$condition['receiver'] = array('eq',$user_id);
    	$order = M('kd_order')->where($condition)->order('receivetime desc')->select();
    	
    	
    	$this->assign('user_id',$user_id);
    	$this->assign('order',$order);
    	return $this->fetch();
    }
}