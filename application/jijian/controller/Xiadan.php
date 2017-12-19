<?php
namespace app\jijian\controller;
use app\home\logic\UsersLogic;
use app\jijian\logic\JijianLogic;
use Think\Db;
class Xiadan extends MobileBase {
    public function _initialize(){
        parent ::_initialize();
        $sitting = M('kd_sitting')->where('name','jijian')->find();
       
        $value = $sitting['value'];
        if($sitting['status'] == 0 ){
            $this->redirect('jijian/index/errortime', array('errmsg' => $value), 2 , '页面跳转中...');
        }
    }
    
    
    public function xiadan()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
       
       
       // $logic = new JijianLogic();
       // $time = $logic->check_time_jijian();
        //if($time==false){
           // $this->redirect('kuaidi/index/errortime', array('cate_id' => 2), 1, '页面跳转中...');
        //}
        
    
        if($user_id == 0){
            $this->error('请先登陆');
        }
        $address_id = I('address_id/d');
        if($address_id)
            $address = M('user_address')->where("address_id", $address_id)->find();
        else
            $address = M('user_address')->where(['user_id'=>$user_id,'is_default'=>1])->find();
    
        if(empty($address)){
            header("Location: ".U('Mobile/User/add_address',array('source'=>'kd','type'=>'ji')));
        }else{
            $this->assign('address',$address);
        }
    
        $type = I('type');
        
        //有点问题，暂时强制为1，
        $qiang = 1;
        /*
        $qiang = I('qiang');
        */
        if($qiang==1){
            $title = "快递员上门";
        }elseif($qiang==0){
            $title = "驿站寄件";
        }
       
        
       
        
        $this->assign('title',  $title);
        $this->assign('qiang',  $qiang);
        $this->assign('type',  $type);
     
    
        return $this->fetch();
    }
    
    public function xiadan_add(){
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
    
        $address_id = I("address_id/d"); //  收货地址id
        $user_note = trim(I('user_note'));   //买家留言

       
       
        $type  = I("type");
        $kuaidi = M('kd')->where("type", $type)->find();
        $order_amount = 1 ;
        $kuaidi_name = '寄件';

        $address = M('UserAddress')->where("address_id", $address_id)->find();
    
        if($address['sushe']==null){
            $this->error('宿舍不能为空，请重新编辑地址');
        }
    
        $data = array(
            'order_sn'         => date('YmdHis').rand(1000,9999), // 订单编号
            'user_id'          =>$user_id, // 用户id
            'user_name'          =>$user_name, // 用户名
            'consignee'        =>$address['consignee'], // 收货人
            'province'         =>$address['province'],//'省份id',
            'city'             =>$address['city'],//'城市id',
            'district'         =>$address['district'],//'县',
            'twon'             =>$address['twon'],// '街道',
            'address'          =>$address['address'],//'详细地址',
            'mobile'           =>$address['mobile'],//'手机',
            'zipcode'          =>$address['zipcode'],//'邮编',
            'sushe'            =>$address['sushe'],//'宿舍',
            'duanhao'            =>$address['duanhao'],//'短号',
            'type'    =>           $type, //'物流编号',
            'name2'    =>   I('name2'), //'收件人名称',                为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            'mobile2'    =>   I('mobile2'), //'收件人手机',
            'address2'    =>   I('address2'), //'地址',
        	'qiang'    =>   1, //抢,
        	'kuaidi_name'     => '寄件',
            'order_amount'     => $order_amount,
            'add_time'         =>date('Y-m-d H:i:s'), // 下单时间
            'user_note'        =>$user_note, // 用户下单备注
           
        );
    
        
        
        
        
        $order_id = M("kd_order")->insertGetId($data);
    
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '您提交了寄件订单',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>date('Y-m-d H:i:s'),
        );
        
        M('kd_order_action')->insertGetId($action_info);
    
        
        $extra = M('kd_order_ji')->where(array('order_sn'=>$order_sn))->find();
        
        $data['order_id'] = $order_id ;
       
        if(!$extra){
        	
        M('kd_order_ji')->save($data);
        
        }else{
        	M('kd_order_ji')->where(array('order_sn'=>$order_sn))->save($data);
        }
        
       // dump($order_id);
        
       header("Location: ".U('pay/payment/kuaidi',array('order_id'=>$order_id,'source'=>'kuaidi' )));
        
       //header("Location: ".U('jijian/send/chenggong',array('order_id'=>$order_id,'source'=>'ji' )));
       
    }
   
    
       
}