<?php
namespace app\box\controller;
use app\home\logic\UsersLogic;
use app\kuaidi\logic\KuaidiLogic;
use Think\Db;
class Xiadan extends MobileBase {
    
    public function _initialize(){
        parent ::_initialize();
        $sitting = M('kd_sitting')->where('name','box')->find();
        $value = $sitting['value'];
        if($sitting['status'] == 0 ){
            $this->redirect('kuaidi/index/errortime', array('errmsg' => $value), 2 , '页面跳转中...');
        }
    }
    
    
    public function xiadan()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        
        $type = 'box';
       
       // $logic = new KuaidiLogic();
       // $time = $logic->check_time();
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
            header("Location: ".U('Mobile/User/add_address',array('source'=>'box')));
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
        
        $address_id = I("address_id/d"); //  收货地址id
        $user_note = trim(I('user_note'));   //买家留言
        $code = trim(I('code'));   //取件码
        
        if(preg_match(" /^[0-9a-zA-Z_]{6,16}$/",$code)){
            //通过
        }else{
            $this->error('不能包含中文。只需要取件密码，6位数字字母组合');
        }
        
        $box = I('box');
        if($box == null){
            $this->error('快递柜不能为空，请选择');
        }
        
        $kuaidi_name = I("kuaidi_name");
        $order_amount = I("order_amount");
        $type  = 'box';

        $address = M('UserAddress')->where("address_id", $address_id)->find();
    
        if($address['sushe']==null){
            $this->error('宿舍不能为空，请重新编辑地址');
        }
        
        $qiang = M('kd')->where("type", $type)->getField('qiang');
        
        
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
            'action_note'     => '您提交了快递柜订单，未拿',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>date('Y-m-d H:i:s'),
        );
        M('kd_order_action')->insertGetId($action_info);
    
        $songda_time = date('Y-m-d H:i:s');
        
        $extra = M('kd_order_extra')->where(array('order_id'=>$order_id))->find();
        $extra_data['order_id'] = $order_id;
        $extra_data['code'] = $code;
        $extra_data['box'] = $box;
        if(!$extra){
            M('kd_order_extra')->save($extra_data);
        }else{
            M('kd_order_extra')->where(array('order_id'=>$order_id))->save($extra_data);
        }
        
        
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