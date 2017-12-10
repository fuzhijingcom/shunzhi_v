<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;
use app\work\logic\MoneyLogic;
use think\Page;
use think\Db;
class Money extends MobileBase {

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
            $this->error('权限不够');
            //$this->redirect(U("work/error/noauth"));
            exit;
        }
        
    }
   
    public function index(){
        $all = $this->all();
        $this->assign('all', $all);
        
        
        $tixian_num = M('withdrawals')->where('status',0)->count('id');
        $this->assign('tixian_num', $tixian_num);
        return $this->fetch();
    }
    
    public function account()
    { 
        $money_list = $this->money_list();
        
        $all = $this->all();
      
        $this->assign('all', $all);
        $this->assign('money_list', $money_list);
        return $this->fetch();
    }
    
    
    public function account_log(){
        $user_id  =  I('user_id');
    
        $account_log = M('account_log')->where('user_id',$user_id)->order('log_id desc')->select();
        $this->assign('user_id', $user_id);
        $this->assign('account_log', $account_log);
        return $this->fetch();
    }
    
    public function withdraw_log(){
    	$user_id  =  I('user_id');
    	
    	$withdrawals= M('withdrawals')->where(array('user_id'=>$user_id,'status'=>1))->order('id desc')->select();
    	$this->assign('user_id', $user_id);
    	$this->assign('withdrawals', $withdrawals);
    	return $this->fetch();
    }
    
    
    
    public function tixian(){
        
        $list = M('withdrawals')->where('status',0)->order('id desc')->select();
        $this->assign('list', $list);
        return $this->fetch();
    }
    public function tixian_action(){
        $id = I('id/d');
        $detail = M('withdrawals')->where('id',$id)->find();
        $user_id = $detail['user_id'];
        
        $user_money = M('users')->where('user_id',$user_id)->getField('user_money');

        $kefu = session('user.nickname');
        
        if((float)$user_money < (float)$detail['money']){
            $beizhu = '拒绝提现，余额不够本次提现，请重新申请提现';
            $status = 2;
            M('withdrawals')->where('id',$id)->save(array('remark'=>$beizhu,'status'=>$status));
            $logic = new MoneyLogic();
            $data = $logic->tixian($user_id,$id,$kefu,$beizhu,$status);
            $this->error('只剩下'.$user_money.'元，不够本次提现，已取消该次提现','work/money/index');
        }
        
        if(IS_POST){
            $payresult = accountLog($user_id,-$detail['money'],-$detail['money'],"提现{$detail['money']}元,扣除{$detail['money']}积分");
            
            if($payresult == true){
                $remark = '提现成功，已到账，请查收。';
                $status = 1;
                M('withdrawals')->where('id',$id)->save(array('remark'=>$remark.'客服：'.$kefu,'status'=>$status));
                $logic = new MoneyLogic();
                $data = $logic->tixian($user_id,$id,$kefu,$remark,$status);
                $this->success('提现成功','work/money/index');               
            }else{
                $this->error('服务器开小差了，操作失败','work/money/index');
            }
                
        }
        
        
        $this->assign('detail', $detail);
        return $this->fetch();
    }
    
    private function money_list(){
        $condition['user_id'] = array('gt',1);
        $condition['user_money'] = array('egt',0.01);
        $user = M('users')->field('user_id,user_money')->where($condition)->order('user_money desc')->select();
        
        return $user;
    }
    private function all(){
        $all_money  = M('users')->field('user_money')->where($condition)->select();
        $c= count($all_money);
        
        for ($i=0;$i<$c;$i++){
            $m =  (float)$all_money[$i]['user_money'];
        
            $all = $all + $m;
        }
        
        return $all;
    }
}