<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;

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

        
        $grade =  M('yuangong') ->where("yid",$this->user_id)->getField('grade');   
        $this->assign('grade',$grade);
        
        //判断是不是抢单员
        $qiangdan =  M('users_qiang') ->where("user_id",$this->user_id)->find(); 
        
        if($grade == 0 || !$grade){
            
            $shangjia =  M('admin_sj') ->where("uid",$this->user_id)->find();
            if($shangjia['is_validated'] == 1){
                $this->redirect(U("shangjia/index/index"));
                exit;
            }elseif ($qiangdan !== NULL){
            
            	$this->redirect(U("qiangdan/index/index"));
            	exit;
            	
            }else{	
              
            
            
            $this->redirect(U("work/error/noauth"));
            exit;
           
            }
        }
        
    }
   
    public function index(){
        $ref = I('ref');
        if(!$ref || $ref == null){
            $shangjia =  M('admin_sj') ->where("uid",$this->user_id)->find();
            if($shangjia['is_validated'] == 1){
                $this->redirect(U("shangjia/index/index"));
                exit;
            }
        }
        
        return $this->fetch();
    }
    
   
    public function search(){
        $name = I('name');
        
        if(strpos($name,'栋') !== false){
            $condition['sushe'] = array('like','%'.$name.'%');
        }
        elseif(is_numeric($name)==true){
            
            if(substr($name , 0 , 1) == '1'){
                $condition['mobile'] = array('like','%'.$name.'%');
            }else{
                $condition['duanhao'] = array('like','%'.$name.'%');
            }
            
        }elseif(strpos($name,'#') !== false){
            $condition['sushe'] = array('like','%'.$name.'%');
        }else{
            $condition['consignee'] = array('like','%'.$name.'%');
        }
         

        $this->assign('name', $name);
 
       
        $condition['pay_status'] = array('eq',1);
        $read = D('kd_order')->where($condition)->order('order_id desc')->select();

        $this->assign('read', $read);
        
        
        
        return $this->fetch();
        
    }
    
    
    public function yuangong() {
        
        $grade = M('yuangong') ->where("yid",$this->user_id)->getField('grade');
        if($grade < 7 ){
            $this->error('权限不够');
            exit;
        }
        
        $yuangong_model = M('yuangong');
        $sessionuid = session('user.user_id');
        $this->assign('sessionuid',$sessionuid);
    
        if (!$sessionuid) {
           $this->error('请先登录');
            exit;
        }
    
        if($_POST) {				
    
            $this->_add_yuangong($yuangong_model);
        }
    
        $condition['grade'] =  array('neq',0);
        
        $yuangong = $yuangong_model->where($condition)->order('grade desc')->select();
        
        $this->assign('yuangong',$yuangong);
        return $this->fetch();
    }
    
    public function lizhi(){
        $condition['grade'] =  array('eq',0);
        $yuangong = M('yuangong')->where($condition)->order('grade desc')->select();
        $this->assign('yuangong',$yuangong);
        return $this->fetch();
    }
    
    public function yuangongedit() {
        $yuangong_model = M('yuangong');
         
        $id = $_GET['id'];
        $yid = $yuangong_model->where("id='$id'")->getField('yid');
         
    
        $this->assign('yid',$yid);
    
        $sessionuid = session('user.user_id');
        $nowgrade = $yuangong_model->where("yid='$sessionuid'")->getField('grade');
       
        if($nowgrade==null){
            $this->error('你的ID还没有录入，不能进行修改操作');
        }
        if($nowgrade<7){
            $this->error('你没有权限更改，只有（主管）、（客服权限）、（ 管理权限 ）可以更改！请联系他们进行更改。');
        }
    
        $name = $yuangong_model->where("id='$id'")->getField('name');
        $this->assign('name',$name);
        $yuangong = D('yuangong')->where("id='$id'")->select();
        $this->assign('yuangong',$yuangong);
    
        if (IS_POST) {
            $map['name']  = I('post.name','','htmlspecialchars');
            $map['yid']  = I('post.yid','','htmlspecialchars');
            $map['grade']  = I('post.grade','','htmlspecialchars');
            $yuangong_model->where(array('yid'=>$yid))->save($map);
           // $yuangong_model->execute("update pre_yuangong set name='$name' , grade='$grade' , yid='$yid' where id=$id");
            $this->redirect(U("work/index/yuangong" ));
        }
    
         return $this->fetch();
    }
    
    private function _add_yuangong($yuangong_model) {		//增加员工
        $data['yid'] = I('post.yid','','htmlspecialchars');
        $data['grade'] = I('post.grade','','htmlspecialchars');
        $data['name'] = I('post.name','','htmlspecialchars');
        $postuid = I('post.yid','','htmlspecialchars');
    
        if($_POST["yid"] == null){
            $this->error('请检查，ID 不能为空');
        }
    
        $nowgrade = $yuangong_model->where("yid='$postuid'")->getField('grade');
    
        if(!empty($nowgrade)){
            $this->error('ID已经存在，请不要重复添加。');
        }
    
        if($_POST["grade"] == null){
            $this->error('请检查，级别 不能为空');
        }
        if($_POST["name"] == null){
            $this->error('请检查，名字 不能为空');
        }
    
        $yuangong_model->add($data);
    }
 
    
    public function ajaxGetMore(){
    	$p = I('p/d',1);
    	$favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1")->order('goods_id DESC')->page($p,10)->cache(true,TPSHOP_CACHE_TIME)->select();//首页推荐商品
    	$this->assign('favourite_goods',$favourite_goods);
    	return $this->fetch();
    }
}