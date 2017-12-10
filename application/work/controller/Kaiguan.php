<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use think\Page;
use think\Db;
class Kaiguan extends MobileBase {

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
    
    
    public function kd(){
        $kuaidi = M('kd')->order('shunxu')->select();
        
        $this->assign('kuaidi', $kuaidi);

        return $this->fetch();
    
    }
    
    public function kd_change(){
    	$id = I('id');
    	$kuaidi = M('kd')->where('id',$id)->find();
    	
    	if(IS_POST){
    		
    		if( (int)I('money') >= (int)I('price') ){
    			$this->error('佣金不能为负，平台扣除金额不能大于下单金额');
    			exit;
    		}
    		
    		if(I('status') == 'on'){
    			$data['status'] = 1;
    		}
    		
    		$data['price'] = I('price');
    		$data['shunxu'] = I('shunxu');
    		$data['money'] = I('money');

    		M('kd')->where('id',$id)->save($data);
    		$this->success('更改成功','kd');
    		
    	}
    	
    	
    	
    	$this->assign('kuaidi', $kuaidi);
    	
    	return $this->fetch();
    }
    
    
}