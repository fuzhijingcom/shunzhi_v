<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;
use app\work\logic\YuangongLogic;

use think\Page;
use think\Db;
class Job extends MobileBase {

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
   
    public function add(){
       
      if(IS_POST){
          $data['title'] = I('title');
          $data['content'] = I('content');
          if(!$data['title'] || !$data['content']){
              $this->error('标题、内容不能为空');
          }
          
          switch (I('deadline')) {
              case 1:
                $data['deadline'] = (int)time() + 86400;
              break;
              
              case 3:
                  $data['deadline'] = (int)time() + 259200;
              break;
                  
              case 7:
                  $data['deadline'] = (int)time() + 604800;
              break;
              
              default:
               $data['deadline'] = (int)time() + 604800;
              break;
          }
          
          $data['description'] = I('description');
          $data['cat_id'] = 93;
          $data['is_open'] = 1;
          $data['publish_time'] =$data['add_time'] = time();
          M('article')->save($data);
          $this->redirect(U("work/job/joblist"));
      }
        
        return $this->fetch();
    }
    
    

    public function joblist(){
        $list = M('article')->where(array('cat_id'=>93,'is_open'=>1))->order('article_id desc')->select();
    
        $this->assign('list',$list);
    
    
        return $this->fetch();
    }
    
    public function delete(){
        $id = I('id');
        
        M('article')->where(array('article_id'=>$id))->save(array('is_open'=>0));
        
        $this->redirect(U("work/job/joblist"));
    }
}