<?php
namespace app\work\controller;
use app\home\logic\UsersLogic;
use app\work\logic\WorkLogic;

use think\Page;
use think\Db;
class Kuaidi extends MobileBase {

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
      
        $uid = session('user.user_id');
        if ($uid == null && is_weixin() == true) {
            $this->error('请先登录');
            exit;
        }
        if ($uid == null && is_weixin() == false) {
            $this->error('请在微信中打开');
            //header("Location: " . "http://www.yykddn.com/mobile" );
            exit;
        }
        $username = session('user.nickname');
        // dump(session());
        
        //显示数量
        $kd = M("kd_order");
        
        
       $date = date("Y-m-d");
        //$condition['add_time'] = array('like','%'.$date.'%');
        
       $condition['order_id'] = array('gt',13000);
        
        $condition['pay_status'] = array('eq', 1 );
       // $condition['qiang'] = array('eq', 1 );
        $condition['order_status'] = array('eq', 0 );
        
        $todaykuaidi['type'] = array('eq','yd');
        $todayCount = $kd->where($condition)->count("type");
    
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        
        $yesterdaycondition['order_status'] = array('eq', 0 );
        $yesterdaycondition['add_time'] = array('like','%'.$yesterday.'%');
        $yesterdayCount = $kd->where($yesterdaycondition)->count("type");
        
        
        $ydkuaidi['type'] = array('eq','yd');
        $ydCount = $kd->where($condition)->where($ydkuaidi)->count("type");
    
        $htkuaidi['type'] = array('eq','ht');
        $htCount = $kd->where($condition)->where($htkuaidi)->count("type");
    
        $ttkuaidi['type'] = array('eq','tt');
        $ttCount = $kd->where($condition)->where($ttkuaidi)->count("type");
    
        $stkuaidi['type'] = array('eq','st');
        $stCount = $kd->where($condition)->where($stkuaidi)->count("type");
    
        $jdkuaidi['type'] = array('eq','jd');
        $jdCount = $kd->where($condition)->where($jdkuaidi)->count("type");
    
        $wpkuaidi['type'] = array('eq','wp');
        $wpCount = $kd->where($condition)->where($wpkuaidi)->count("type");
    
        $kjkuaidi['type'] = array('eq','kj');
        $kjCount = $kd->where($condition)->where($kjkuaidi)->count("type");
    
        $gtkuaidi['type'] = array('eq','gt');
        $gtCount = $kd->where($condition)->where($gtkuaidi)->count("type");
    
        $gtkuaidi['type'] = array('eq','gt');
        $gtCount = $kd->where($condition)->where($gtkuaidi)->count("type");
    
        $qfkuaidi['type'] = array('eq','qf');
        $qfCount = $kd->where($condition)->where($qfkuaidi)->count("type");
    
        $tmkuaidi['type'] = array('eq','tm');
        $tmCount = $kd->where($condition)->where($tmkuaidi)->count("type");
    
        $yskuaidi['type'] = array('eq','ys');
        $ysCount = $kd->where($condition)->where($yskuaidi)->count("type");
    
        $rfdkuaidi['type'] = array('eq','rfd');
        $rfdCount = $kd->where($condition)->where($rfdkuaidi)->count("type");
    
        $yzkuaidi['type'] = array('eq','yz');
        $yzCount = $kd->where($condition)->where($yzkuaidi)->count("type");
    
        $emskuaidi['type'] = array('eq','ems');
        $emsCount = $kd->where($condition)->where($emskuaidi)->count("type");
    
        $ankuaidi['type'] = array('eq','an');
        $anCount = $kd->where($condition)->where($ankuaidi)->count("type");
        
        $snkuaidi['type'] = array('eq','sn');
        $snCount = $kd->where($condition)->where($snkuaidi)->count("type");
        
        $wxkuaidi['type'] = array('eq','wx');
        $wxCount = $kd->where($condition)->where($wxkuaidi)->count("type");
        
        $ddkuaidi['type'] = array('eq','dd');
        $ddCount = $kd->where($condition)->where($ddkuaidi)->count("type");
       
        $dbkuaidi['type'] = array('eq','db');
        $dbCount = $kd->where($condition)->where($dbkuaidi)->count("type");
        
        
        $boxCount = M('kd_order_box')->where($condition)->count("type");
        
        
        $this->assign('boxCount', $boxCount);
        $this->assign('ydCount', $ydCount);
        $this->assign('htCount', $htCount);
        $this->assign('ttCount', $ttCount);
        $this->assign('stCount', $stCount);
        $this->assign('jdCount', $jdCount);
        $this->assign('wpCount', $wpCount);
        $this->assign('kjCount', $kjCount);
        $this->assign('gtCount', $gtCount);
        $this->assign('qfCount', $qfCount);
        $this->assign('tmCount', $tmCount);
        $this->assign('ysCount', $ysCount);
        $this->assign('rfdCount', $rfdCount);
        $this->assign('yzCount', $yzCount);
        $this->assign('emsCount', $emsCount);
        $this->assign('anCount', $anCount);
        $this->assign('snCount', $snCount);
        $this->assign('wxCount', $wxCount);
        $this->assign('ddCount', $ddCount);
        $this->assign('dbCount', $dbCount);
    
        $this->assign('todayCount', $todayCount);
    
        $this->assign('yesterdayCount', $yesterdayCount);
        
        $this->assign('uid', $uid);
        $this->assign('username', $username);
  
        $status = M('kd');
        $this->assign('ydsitting', $status->where('type','yd')->getField('status'));
        $this->assign('htsitting', $status->where('type','ht')->getField('status'));
        $this->assign('ttsitting', $status->where('type','tt')->getField('status'));
        $this->assign('stsitting', $status->where('type','st')->getField('status'));
        $this->assign('wpsitting', $status->where('type','wp')->getField('status'));
        $this->assign('kjsitting', $status->where('type','kj')->getField('status'));
        $this->assign('gtsitting', $status->where('type','gt')->getField('status'));
        $this->assign('qfsitting', $status->where('type','qf')->getField('status'));
        $this->assign('tmsitting', $status->where('type','tm')->getField('status'));
        $this->assign('yssitting', $status->where('type','ys')->getField('status'));
        $this->assign('rfdsitting', $status->where('type','rfd')->getField('status'));
        $this->assign('yzsitting', $status->where('type','yz')->getField('status'));
        $this->assign('emssitting', $status->where('type','ems')->getField('status'));
        $this->assign('ansitting', $status->where('type','an')->getField('status'));
        $this->assign('snsitting', $status->where('type','sn')->getField('status'));
        $this->assign('wxsitting', $status->where('type','wx')->getField('status'));
        $this->assign('ddsitting', $status->where('type','dd')->getField('status'));
        $this->assign('dbsitting', $status->where('type','db')->getField('status'));
        $this->assign('boxsitting', $status->where('type','box')->getField('status'));
         
        
         return $this->fetch();
    
    }

    public function read(){
    
     $uid = session('user.user_id');
        if ($uid == null) {
            $this->error('请先登录');
        }

        $username = session('user.nickname');
    
        $searchrealname= $_POST["searchrealname"];
        // dump($searchrealname);
        $this->assign('searchrealname', $searchrealname);
         
        $type= $_GET["type"];
        $this->assign('type', $type);
         
       // if ($type == 'ht') {
          //  header("Location: " . "http://www.yykddn.com/codetoany/getcode.php?auk=rr" );
           // exit;
       // }
        
        
        $condition['order_id'] = array('gt',13000);
        
        
        $date = $_POST["date"];
       
       
        $order_status = $_POST["order_status"];
        if($order_status == NULL) {
            $order_status = 0 ;
            if($date == NULL){
                $date = date("Y-m-d");
            
            //$condition['add_time'] = array('like','%'.$date.'%');
            }
        }
    
        if( $order_status == 1 ){
           if($date == NULL){
               $date = date("Y-m-d");
            }
            $condition['add_time'] = array('like','%'.$date.'%');
            
        }
        
        
      
       
        $this->assign('date', $date);
        
        
        
        $this->assign('order_status', $order_status);
 
        
        
        
        
        $condition['consignee'] = array('like','%'.$searchrealname.'%');
        $condition['qiang'] = array('eq', 1 );
        $condition['pay_status'] = array('eq',1);
        if($type != 'today'){
            $condition['type'] = array('eq',$type);
        }
    
        $condition['order_status'] = array('eq',$order_status);

        $read = D('kd_order')->where($condition)->order('order_id desc')->select();
    
        $todaytime = date("Y-m-d");
        
        $todaycondition['add_time'] = array('like','%'.$date.'%');
         
    
       // $today_time_first =  D('kd_order') -> order('order_id')->limit(1)->where($todaycondition)->select();
        // dump($today_time_first['0']['id']);
        //$this->assign('today_time_first', $today_time_first['0']['id']);
        
        $list = D('kd')->where("status","1")->select();
        $this->assign('list', $list);
        $this->assign('read', $read);
        return $this->fetch();
    }
    
    
   
    
    public function qianshou() {
        
        $uid = session('user.user_id');
        if ($uid == null) {
              $this->error('请先登录');
        }
    
        $this->assign('uid', $uid);
        $receivetime = date("Y-m-d H:i:s");
       
        $type = $_GET['type'];
        
       
        $this->assign('kuaiai', $qianshou);

        $order_id =  I('order_id');

        
        if (IS_POST) {
            
            //检查是否未支付的订单
            $order_status_check = M('kd_order')->where(array('order_id'=>$order_id))->getField('order_status');
            if( $order_status_check > 0){
               $this->redirect(U("work/kuaidi/read" , array('type' => $type  )));
                exit;
            }
             
            
            
            
            
            $result = M('kd_order')->where('order_id',$order_id)->save(['receiver' => $uid,'receivetime' => $receivetime, 'order_status' => 1 ]);;
        
            $action_info = array(
                'order_id'        =>$order_id,
                'action_user'     =>$uid,
                'action_note'     => '订单已拿，时间：'.$receivetime,
                'status_desc'     =>'签收者：'.$uid, 
                'log_time'        =>date('Y-m-d H:i:s'),
            );
            M('kd_order_action')->insertGetId($action_info);
            
            if ( I('source') == 'riqi' && I('date') !== date('Y-m-d') ) {
                $beizhu = '这是补录签收，快递已经派送成功。如有问题，请截图联系客服。';
            }
           
            $logic = new WorkLogic();
            $data = $logic->push_msg_yina($order_id,$uid,$receivetime,$beizhu);
             
            
            if ($result == 1 && I('source') == 'riqi') {
                $this->redirect(U("work/riqi/search" , array('date' => I('date')  )));
                exit;
            }
            
            if ($result == 1) {
                $this->redirect(U("work/kuaidi/read" , array('type' => $type  )));
            }
            $this->error('签收失败，请联系精哥哥！');
            return;
        }
        
        
        $kd_order = M('kd_order')->where('order_id',$order_id)->find();
       
       
        if(!isset($kd_order)){
            $this->error('快递信息不存在，请重新选择！');
            return;
        }
        
       
       // D('kd_order')->select();
        $this->assign('order',$kd_order);
        
        $yuangongname =  M('yuangong') ->where("yid",$this->user_id)->getField('name');
        $this->assign('yuangongname',$yuangongname);
        
        return $this->fetch();
    }
    
    
    
    public function paijian(){
        $type = I('type');
        if($type==''){
            $this->error('快递类型错误');
        }
        if (!$this->user_id) {
            //$url = SITE_URL.'/work/kuaidi/paijian/type/'.$type;
           // session('url',$url);
           // header("Location: " . "http://www.yykddn.com/codetoany/getcode.php?auk=url" );
            exit;
        }
       
      //  $condition['add_time'] = array('like','%'.date('Y-m-d').'%');
      //where($condition)->
      
        $condition['order_id'] = array('gt',13154);
        $condition['order_status'] = array('neq',7);
        $con['order_status'] = array('neq',5);
       
        $pay_order = M('kd_order')->where(array('pay_status'=>1,'type'=>$type))->where($condition)->where($con)->select();
        $c = count($pay_order);
        $this->assign('c',$c);
        $this->assign('type',$type);
        $this->assign('pay_order',$pay_order);
        return $this->fetch();
    }
    
   
 
}