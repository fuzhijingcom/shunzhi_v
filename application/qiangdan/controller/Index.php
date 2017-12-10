<?php
namespace app\qiangdan\controller;
use app\home\logic\UsersLogic;
use app\work\logic\WorkLogic;


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
   
        
    }
    public function index(){
        $userModel = M('users_qiang');
        $user =$userModel->where("user_id", $this->user_id)->find();
        if(!$user){
            $this->redirect('join');
            exit;
        }
       
        $is_validated = $user['is_validated'];
        
        $this->assign('is_validated',$is_validated);
        
        $school_value = M('users_qiang')->where('user_id',$this->user_id)->getField('school');
        $school= M('school')->where('value',$school_value)->find();
        $this->assign('school',$school);
        return $this->fetch();
        
    }
    /**
     * 申请加入
     * 
     * is_validated == 0 刚申请，待审核
     * is_validated == 1 正常
     * is_validated == 3 审核不通过
     * is_validated == 4 禁止抢单功能
     * 
     */
    public function join()
    {
        if (!$this->user_id) {
           $this->error('还没登陆');
            exit;
        }
    
        $userModel = M('users_qiang');
        $user =$userModel->where("user_id", $this->user_id)->find();
       
       
        if($user !== NULL){
            if($user['is_validated']==1){
                header("location:" . U('qiangdan/index/index'));
                exit;
            }
            if($user['is_validated'] == 0 || $user['is_validated']== 4 ){
               header("location:" . U('qiangdan/index/index'));
                exit;
            }
        }
        
        //先保存起来
        $address = M('user_address')->where(array('is_default' => 1 , 'user_id' => $this->user_id))->find();
         
   
    
        if (IS_POST) {
            $post = I('post.');
 
            if(I('post.tuisong')=='on'){
                $tuisong = 1;
            }else{
                $tuisong = 0;
            }
           
            
           
           
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('file');
            
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    // 成功上传后 获取上传信息
                    // 输出 jpg
                   // echo $info->getExtension();
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                   // echo $info->getSaveName();
                   $imgurl = $info->getSaveName();
                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
                   // echo $info->getFilename();
                }else{
                    // 上传失败获取错误信息
                    echo $file->getError();
                    $this->error('照片上传出错');
                }
            }
            
            if(!$imgurl){
                $this->error('照片上传出错,请重新上传');
            }
            
           $post = array(
                'user_id' => $this->user_id ,
                'is_validated' => 0,
                'tuisong' => $tuisong,
                'openid' => session('user.openid'),
               'imgurl'=> $imgurl,
            ) + $post;
             
            
            
            if(!$user){
              M('users_qiang')->save($post);
           }else{
             M('users_qiang')->where(array('user_id' => $this->user_id))->save($post);
           }
           
          
           
           
            $this->success('保存成功', U('/qiangdan/index/index'));
            exit();
        }
       
        $this->assign('address', $address);
        return $this->fetch();
    }
    /*
     * 
     *  is_validated == 0 刚申请，待审核
    * is_validated == 1 正常
    * is_validated == 3 审核不通过
    * is_validated == 4 禁止抢单功能
     * 
     * */
   
        
    public function qiangdanerror(){
        $is_validated = I('is_validated');
        switch ($is_validated) {
            case 0:
            $note = '待审核';
            break;
        
        case 3:
            $note = '审核不通过';
            break;
                
        case 3:
            $note = '禁止抢单';
            break;
                    
            default:
                ;
            break;
        }
        
        $this->assign('note', $note);
        return $this->fetch();
    }
 
    
    /**
     * 推送开关设置
     */
    
    public function tuisong(){
        if (!$this->user_id) {
            $this->error("还未登录");
            exit;
        }
    
        if(IS_POST){
            $tuisong = I('tuisong');
            if($tuisong == 'on'){
                $map['tuisong'] = 1;
            }else{
                $map['tuisong'] = 0;
            }
    
            M('users_qiang') ->where("user_id", $this->user_id)->save($map);
             
        }
    
       
    
        $userModel = M('users_qiang');
        $is_validated =$userModel->where("user_id", $this->user_id)->getField('is_validated');
    
        $tuisong =$userModel->where("user_id", $this->user_id)->getField('tuisong');
        $this->assign('tuisong', $tuisong);
    
        if($is_validated==null){
            header("Location:" . U('qiangdan/index/index'));
            exit;
        }
        if($is_validated !== 1 ){
            header("Location:" . U('qiangdan/index/index'));
            exit;
        }
         
    
        $this->assign('is_validated', $is_validated);
    
        
        return $this->fetch();
    }
    
    /**
     * 进群验证
     */
    
    public function qun(){
    	$user_id = session('user.user_id');
    	
        if (!$user_id) {
        	header("location:" . U('Mobile/User/login'));
            exit;
        }
        
        $code = I('code');
        if($code !== '4f2016c6b934d55bd7120e5d0e62cce3'){
        	$this->error("加群验证链接已经失效");
        	exit;
        }
        
        
        $userModel = M('users_qiang');
        
        $is_validated =$userModel->where("user_id", $this->user_id)->getField('is_validated');
        if($is_validated !== 1 ){
            header("Location:" . U('qiangdan/index/index'));
            exit;
        }
        
        $qun = $userModel->where('user_id',$this->user_id)->getField('qun');
        
        if($qun  == 1){
            $this->success('可以抢单了，你已经通过验证','index');
        }else{
            M('users_qiang')->where('user_id',$this->user_id)->save(array('qun'=>1));
            $this->success('恭喜你，通过入群培训，可以抢单了','index');
        }
        
    }
    
}