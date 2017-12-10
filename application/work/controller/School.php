<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use think\Page;
use think\Db;
class School extends MobileBase {

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
            $uid = $user['user_id'];
        }
       
        $grade =  M('yuangong') ->where("yid",$uid)->getField('grade');

        if($grade==0){
             $this->redirect(U("work/error/noauth"));
            exit;
        }
    }
    
    public function qrcode(){
    	$condition['value'] = I('school');
    	
    
    	echo $expire;
    	if(IS_POST){
    		$qrcode = I('qrcode');
    		if(!$qrcode){
    			$this->error('不能为空');
    			exit;
    		}
    		$expire = date("Y-m-d",strtotime("+1 week"));
    		M('school')->where($condition)->save(array('qrcode'=>$qrcode,'expire'=>$expire));
    		$this->success('更新成功','index');
    	}
    	$school = M('school')->where($condition)->find();
    	$this->assign('school', $school);
    	return $this->fetch();
    }
    
    public function index(){
        $school_list = M('school')->select();
        
        $this->assign('school_list', $school_list);

        return $this->fetch();
    
    }
    
    public function detail(){
        $condition['school'] = I('school');
        $school = M('users')->field('user_id,school')->where($condition)->select();
        $this->assign('school', $school);
        return $this->fetch();
    }
    
    
}