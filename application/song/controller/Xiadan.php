<?php
namespace app\song\controller;
use app\home\logic\UsersLogic;
use app\kuaidi\logic\KuaidiLogic;
use Think\Db;
class Xiadan extends MobileBase {
    
    public function xiadan()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        
        $type = 'ss';
       
        if($user_id == 0){
            $this->error('请先登陆');
        }
       
        $address_id = I('address_id/d');
        if($address_id)
            $address = M('user_address')->where("address_id", $address_id)->find();
        else
            $address = M('user_address')->where(['user_id'=>$user_id,'is_default'=>1])->find();
    
        if(empty($address)){
            header("Location: ".U('Mobile/User/add_address',array('source'=>'ss')));
        }else{
            $this->assign('address',$address);
        }
    
        $kd_model = M('kd');
        $kuaidi = $kd_model->where("type", $type)->find();
      
        $this->assign('kuaidi', $kuaidi );
        $this->assign('type',  $type);
     
    
        return $this->fetch();
    }
    
    public function xiadan_add(){
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        $school  =   session('user.school');
        
        $address_id = I("address_id/d"); //  寄件人
       
        $address = I('address');//收件人地址
        $mobile = I('mobile');//收件人号码
        $consignee = I('consignee');//收件人姓名
        $duanhao = I('duanhao');
        
        if( $address == null ){
          $this->error('收件信息不能为空，请检查');
        }
        
        /*
        $weight = I('weight/d');
        if(preg_match("/^\d+$/i",$weight)){
        }else{
            $this->error('重量必选，只能是数字');
        }
        
        $distance = I('distance/d');
        */
      
      
        $wupin = I('wupin');
        
       // $user_note = '服务要求：'.$fuwu.'。取送距离：'.$distance.'。重量：'.$weight; 
        $user_note = $wupin;
       // $user_note = '收件人姓名：'.$name.'。收件人号码：'.$mobile.'。收件人地址：'.$address.'取送距离：'.$distance.'。物品：'.$wupin.'。重量：'.$weight.'。备注：'.$beizhu;   //买家留言
        
        $kuaidi_name = I("kuaidi_name");
        
        /*
        $dijia = I("order_amount");//底价5元
        
        if($distance == 4){
            $distance_price = 0;
        }
        
        if($distance >= 4 ){
            $distance_price = ( (int)$distance - 4)  * 1;
        }
        
      
        
        if($weight <=  1){
            $weight_price = 0;
        }
        
        if( $weight >= 1){
            $weight_price =  ( (int)$weight - 1)   * 0.5;
        }
        
        //夜间服务费
        $hour=(int)date("G");
        if($hour >= 22 || $hour <= 7){
            $ye = 5;
        }else{
            $ye = 0;
        }
        
        
        */
        
        $order_amount = I("order_amount");
        
        if(   (int)$order_amount < 3  ){
            $this->error('￥3元起');
            exit;
        }
        
        //$order_amount = (float)$dijia + $distance_price + $weight_price + $ye;
        
        
        $type  = 'ss';

      //  $address = M('UserAddress')->where("address_id", $address_id)->find();
    
       // if($address['sushe']==null){
          //  $this->error('宿舍不能为空，请重新编辑地址');
        //}
        
        $qiang = M('kd')->where("type", $type)->getField('qiang');
        
        
        $data = array(
            'order_sn'         => date('YmdHis').rand(1000,9999), // 订单编号
            'user_id'          =>$user_id, // 用户id
            'user_name'          =>$user_name, // 用户名
            'consignee'        => $consignee, // 收货人
          
            'address'          =>$address,//'详细地址',
            'mobile'           =>$mobile,//'手机',
            'sushe'            =>$address,//'宿舍',
            'duanhao'    =>$duanhao,//'短号',
            'type'    =>      $type, //'物流编号',
            'kuaidi_name'    =>    $kuaidi_name, //'快递名称',                为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            'order_amount'     => $order_amount,
            'add_time'         =>date('Y-m-d H:i:s'), // 下单时间
            'user_note'        =>$user_note, // 用户下单备注
            'qiang'        =>$qiang, // 是否抢单
            'discount'        =>$discount, // 加价多少
            'school'        =>$school, // 哪个学校的
        );
    
        $order_id = M("kd_order")->insertGetId($data);
    
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '您提交了闪送订单，未拿',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>date('Y-m-d H:i:s'),
        );
        M('kd_order_action')->insertGetId($action_info);
    
        
        header("Location: ".U('pay/payment/kuaidi',array('order_id'=>$order_id,'source'=>'box' )));
       
    }
   
    //微信Jssdk 操作类 用分享朋友圈 JS
    public function ajaxGetWxConfig(){
    	$askUrl = I('askUrl');//分享URL
    	$weixin_config = M('wx_user')->find(); //获取微信配置
    	$jssdk = new \app\mobile\logic\Jssdk($weixin_config['appid'], $weixin_config['appsecret']);
    	$signPackage = $jssdk->GetSignPackage(urldecode($askUrl));
    	if($signPackage){
    		$this->ajaxReturn($signPackage,'JSON');
    	}else{
    		return false;
    	}
    }
       
}