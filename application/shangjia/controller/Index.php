<?php
namespace app\shangjia\controller;
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
        }    
        $user_id = session('user.user_id');
        $shangjia =  M('admin_sj') ->where(array('uid'=>$user_id,'is_validated'=>1))->find();     
        if(!$shangjia){
            $this->error('你不是商家');
            exit;
        }
    }
   
    public function index(){
       
        
        
       return $this->fetch();
    }
    

    public function info(){
        $user_id = session('user.user_id');
        $shangjia =  M('admin_sj') ->where(array('uid'=>$user_id,'is_validated'=>1))->find();
        
        if(IS_POST){
            $data['name'] = I('name');
            $data['mobile'] =  I('mobile');
            $data['duanhao']  = I('duanhao');
            M('admin_sj')->where('uid',$user_id)->save($data);
            $this->success('修改成功');
        }        
        $this->assign('shangjia',$shangjia);
        
        return $this->fetch();
    }
    
}