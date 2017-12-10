<?php
namespace app\qiangdan\controller;
use app\home\logic\UsersLogic;
use app\work\logic\WorkLogic;

use think\Page;
use think\Db;
class Tuisong extends MobileBase {

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
    

   
        
    /**
     * 推送开关设置
     */
    
    public function tuisong(){
        if (!$this->user_id) {
            header("Location: " . "http://www.yykddn.com/codetoany/getcode.php?auk=tui" );
            exit;
        }
    
        if(IS_POST){
            $tuisong = I('tuisong');
            if($tuisong == 'on'){
                $map['tuisong'] = 1;
            }else{
                $map['tuisong'] = 0;
            }
    
            M('users_kd') ->where("user_id", $this->user_id)->save($map);
             
        }
    
        $this->assign('title', '推送设置');
    
        $userModel = M('users_kd');
        $is_validated =$userModel->where("user_id", $this->user_id)->getField('is_validated');
    
        $tuisong =$userModel->where("user_id", $this->user_id)->getField('tuisong');
        $this->assign('tuisong', $tuisong);
    
        if($is_validated==null){
            header("Location:" . U('Mobile/renren/index'));
            exit;
        }
        if($is_validated !== 5 ){
            header("Location:" . U('Mobile/renren/index'));
            exit;
        }
         
    
        $this->assign('is_validated', $is_validated);
    
        $order_count = M('order')->where("user_id", $this->user_id)->count(); // 我的订单数
        $goods_collect_count = M('goods_collect')->where("user_id", $this->user_id)->count(); // 我的商品收藏
        $comment_count = M('comment')->where("user_id", $this->user_id)->count();//  我的评论数
        $coupon_count = M('coupon_list')->where("uid", $this->user_id)->count(); // 我的优惠券数量
        $level_name = M('user_level')->where("level_id", $this->user['level'])->getField('level_name'); // 等级名称
        $this->assign('level_name', $level_name);
        $this->assign('order_count', $order_count);
        $this->assign('goods_collect_count', $goods_collect_count);
        $this->assign('comment_count', $comment_count);
        $this->assign('coupon_count', $coupon_count);
        return $this->fetch();
    }
   
 
}