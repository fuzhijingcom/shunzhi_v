<?php
namespace app\kuaidi\controller;
use app\home\logic\UsersLogic;
use app\mobile\logic\Jssdk;
use app\mobile\logic\Push;
use app\mobile\logic\Pushji;
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
            $this->openid = $user['openid'];
            $this->assign('user',$user); //存储用户信息
           
        }
    }
    
    
    public function index(){
        $kuaidi = M('kd')->where('status','1')->order('shunxu desc')->select();
       
        //dump($kuaidi);
        
        $this->assign('kuaidi', $kuaidi );
        //echo '这里是快递服务';
        
        return $this->fetch();
    }

  
    /**
     * 下单
     */
    public function errortime(){
        $errmsg = I('errmsg');
        $this->assign('errmsg', $errmsg );
        return $this->fetch();
    }
    public function xiadan()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        
        $type = I('type');
        
        if ($type == '' || !$type ) {
            header("Location: ".U('Mobile/kuaidi/index'));
           exit;
        }
        
        if ($type !== 'ji' ) {
        
            header("Location: " . "http://mp.weixin.qq.com/s/HJFJPbASLTkfwJ1mSitjKw" );
            exit;
            
            
            
            
            $hour=(int)date("G");
            if($hour == 12){
                $ii=(int)date("i");
                if($ii >= 15){
                    header("Location: ".U('Mobile/kuaidi/errortime',array('errmsg'=>'在12:15——00:00不能下单')));
                    exit;
                }
             
            }
            if($hour == 13){
                header("Location: ".U('Mobile/kuaidi/errortime',array('errmsg'=>'在12:15——14:00不能下单')));
                exit;
            }
            if($hour == 17){
                $i=(int)date("i");
                if($i >= 30){
                    header("Location: ".U('Mobile/kuaidi/errortime',array('errmsg'=>'在17:30——00:00不能下单')));
                    exit;
                }
            }
            if($hour >=18){
                header("Location: ".U('Mobile/kuaidi/errortime',array('errmsg'=>'在17:30——00:00不能下单')));
                exit;
            }
       
        }
       
        if($_POST){
           
            
            $address_id = I("address_id/d"); //  收货地址id
            //$shipping_code =  I("shipping_code"); //  物流编号
           // $beizhu = I('beizhu'); // 备注
           // $couponTypeSelect =  I("couponTypeSelect"); //  优惠券类型  1 下拉框选择优惠券 2 输入框输入优惠券代码
           // $coupon_id =  I("coupon_id/d"); //  优惠券id
           // $couponCode =  I("couponCode"); //  优惠券代码
           // $pay_points =  I("pay_points/d",0); //  使用积分
            //$user_money =  I("user_money/f",0); //  使用余额
            if($type=='ji'){
                if(I('sname')==null || I('smobile')==null || I('saddress')==null){
                    $this->error('下单失败，要全部填写');
                    exit;
                }

                $user_note = trim("收件人姓名：".I('sname')."，收件人电话：".I('smobile')."，收件人地址：".I('saddress'));   //买家留言
            }else{
                $user_note = trim(I('user_note'));   //买家留言
            }
            
            
           
            
            $kuaidi_name = I("kuaidi_name");
            $type  = I("type");
            $share_id  = I("share_id");  //分享ID，推广
            
            
            
            $address = M('UserAddress')->where("address_id", $address_id)->find();
            
            if($address['sushe']==null){
                $this->error('宿舍不能为空，请重新编辑地址');
            }
            
                //$shipping = M('Plugin')->where("code", $shipping_code)->cache(true,TPSHOP_CACHE_TIME)->find();
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
                'kuaidi_name'    =>$kuaidi_name, //'快递名称',                为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
                //'invoice_title'    =>$invoice_title, //'发票抬头',                
               // 'goods_price'      =>$car_price['goodsFee'],//'商品价格',
               // 'shipping_price'   =>$car_price['postFee'],//'物流价格',                
               // 'user_money'       =>$car_price['balance'],//'使用余额',
               // 'coupon_price'     =>$car_price['couponFee'],//'使用优惠券',                        
               // 'integral'         =>($car_price['pointsFee'] * tpCache('shopping.point_rate')), //'使用积分',
               // 'integral_money'   =>$car_price['pointsFee'],//'使用积分抵多少钱',
               // 'total_amount'     =>($car_price['goodsFee'] + $car_price['postFee']),// 订单总额
               // 'order_amount'     =>$car_price['payables'],//'应付款金额',  
                'order_amount'     => '2.00',
                'add_time'         =>date('Y-m-d H:i:s'), // 下单时间                
              //  'order_prom_id'    =>$car_price['order_prom_id'],//'订单优惠活动id',
              //  'order_prom_amount'=>$car_price['order_prom_amount'],//'订单优惠活动优惠了多少钱',
                'user_note'        =>$user_note, // 用户下单备注         
                'share_id'        =>$share_id, // 分享ID
        );
            
        $data['order_id'] = $order_id = M("kd_order")->insertGetId($data);
        
if($type=='ht'){
    $dataht = array(
        'order_amount'     => '2.00',
    );
    M("kd_order")->where(array('order_id' => $order_id ))->save($dataht);
}
        $order = $data;//M('Order')->where("order_id", $order_id)->find();
       
           // return array('status'=>-8,'msg'=>'添加订单失败','result'=>NULL);
                
        // 记录订单操作日志
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,            
            'action_note'     => '您提交了订单，未拿',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>date('Y-m-d H:i:s'),
        );
        M('kd_order_action')->insertGetId($action_info);             
        
        //return array('status'=>1,'msg'=>'提交订单成功','result'=>$order_id); // 返回新增的订单id
       
       // if(!$order_id)
            
        $order_sn = $data['order_sn'];
        if($type !== 'ji'){
            header("Location: ".U('Mobile/kuaidi/cart4',array('order_id'=>$order_id,'type'=>'kd' )));
            exit;
        }else{
            header("Location: ".U('Mobile/kuaidi/chenggong',array('order_id'=>$order_id,'type'=>$type )));
        }
            
          
     }

        if($this->user_id == 0)
            $this->error('请先登陆',U('Mobile/User/login'));
        $address_id = I('address_id/d');
        if($address_id)
            $address = M('user_address')->where("address_id", $address_id)->find();
        else
            $address = M('user_address')->where(['user_id'=>$this->user_id,'is_default'=>1])->find();
        
        if(empty($address)){
        	header("Location: ".U('Mobile/User/add_address',array('source'=>'kd')));
        }else{
        	$this->assign('address',$address);
        }
        
        $kd_model = M('kd');
        $kuaidi_name = $kd_model->where("type", $type)->getField('kuaidi_name');
        $kuaidi_img = $kd_model->where("type", $type)->getField('kuaidi_img');
         
        $this->assign('kuaidi_img', $kuaidi_img );
        $this->assign('type',  $type);
        $this->assign('kuaidi_name', $kuaidi_name );

        return $this->fetch();
    }
    
    
    public function userid()
    {
       
        $user = Db::table('tp_users_kd')->field('user_id')->where(array('tuisong' => 1 , 'is_validated' => 5 ))->select();
        return $user;
    
    }
    public function get_openid_by_uid($uid)
    {
         
        $openid = M('users')->where(array('user_id' => $uid))->getField('openid');
        return $openid;
    
    }

    //不同了
    public function user()
    {
         $join = [
            ['users w','a.user_id=w.user_id'],
        ];
        $user = Db::table('tp_users_kd')->alias('a')->join($join)->field('w.openid')->where(array('a.tuisong' => 1 , 'a.is_validated' => 5 ))->select();
        return $user;

    
    }
    
   public function chenggong() {
       $order_id = I('order_id/d');
       $order = M('kd_order')->where("order_id", $order_id)->find();
       $type = I('type');
      
       
       $logic = new Jssdk();
       $data = $logic->push_msg_ok($this->openid,$type,$order['order_sn'],$order['consignee'],$order['mobile'],$order['sushe']);
       
       $this->assign('type',$type);
       $this->assign('order',$order);
      return $this->fetch();
   }
   
   public function send_all() {
       //curl 请求
       //发送成功通知
       //return
       $type = I('type');
       $order_sn = I('order_sn');
       $consignee = I('consignee');
       $mobile = I('mobile');
       $sushe = I('sushe');
       //dump(I(''));
        if($type == 'ht'){
            $user = $this->user();
            $c = count($user);
                //dump($user);
                //dump($c);
            $pushlogic = new Push();
        
            for ($i = 0; $i < $c; $i++){
                $openid =  $user[$i]['openid'];
                $pushlogic->push_msg_all($openid,$order_sn,$consignee,$mobile,$sushe);
                }
          $this->success('发送通知成功！',U('/mobile/kuaidi/order_list'));
        }
        
        
   }
   
   
   public function send_all_jijian() {
       //curl 请求
       //发送成功通知
       //return
       $type = I('type');
       $order_sn = I('order_sn');
       $consignee = I('consignee');
       $mobile = I('mobile');
       $sushe = I('sushe');
      
   
       if($type == 'ji'){
           $user = $this->user();
           $c = count($user);
           //dump($user);
           //dump($c);
           $pushlogic = new Pushji();
   
           for ($i = 0; $i < $c; $i++){
               $openid =  $user[$i]['openid'];
               $pushlogic->push_msg_all_jijian($openid,$order_sn,$consignee,$mobile,$sushe);
           }
        $this->success('发送通知成功！',U('/mobile/kuaidi/order_list'));
       }
   }
    
   /*
    * 订单详情
    */
   public function order_detail()
   { 
       $id = I('get.id/d');
       
       if($this->user_id == 0){
           session('id',$id);
           header("Location: " . "http://www.yykddn.com/codetoany/getcode.php?auk=detail" );
       }
       
      if(!$id){
          $id = session('id');
      }
       
       $map['order_id'] = $id;
       $map['user_id'] = $this->user_id;
 
       $order_info = M('kd_order')->where($map)->find();
       
       if ($order_info['user_id'] !== $this->user_id) {
           $this->error('该订单不是你的，不能查看信息');
           exit;
       }
       
       
       $order_info = set_btn_order_status($order_info);  // 添加属性  包括按钮显示属性 和 订单状态显示属性
       
       if (!$order_info) {
           $this->error('没有获取到订单信息');
           exit;
       }
       //获取订单商品
      // $model = new UsersLogic();
      // $data = $model->get_order_goods($order_info['order_id']);
       //$order_info['goods_list'] = $data['result'];
       //$order_info['total_fee'] = $order_info['goods_price'] + $order_info['shipping_price'] - $order_info['integral_money'] -$order_info['coupon_price'] - $order_info['discount'];
   
       $region_list = get_region_list();
      //$invoice_no = M('DeliveryDoc')->where("order_id", $id)->getField('invoice_no', true);
      // $order_info[invoice_no] = implode(' , ', $invoice_no);
       
       //获取订单操作记录
       $order_action = M('kd_order_action')->where(array('order_id' => $id))->select();

       $this->assign('region_list', $region_list);
       $this->assign('order_info', $order_info);
       $this->assign('order_action', $order_action);
   
       //if (I('waitreceive')) {  //待收货详情
          // return $this->fetch('wait_receive_detail');
      // }
       return $this->fetch();
   }
   
   /*
    * 订单列表
    */
   public function order_list()
   {
       $where['user_id'] = $this->user_id;
       $order_status = I('get.order_status');
       //条件搜索
       if($order_status !== 0 ){
           
           $where['order_status'] = $order_status;
           
       }
       
       $where['pay_status'] = 1 ;
      
       $count = M('kd_order')->where($where)->count();
       
       
       //dump($count);
      
       
       $Page = new Page($count, 10);
       $show = $Page->show();
       $order_str = "order_id DESC";
       $order_list = M('kd_order')->order($order_str)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
   
      
       
       
       /*
       //统计订单商品数量
       foreach ($order_list as $key => $value) {
           $count_goods_num = '';
           foreach ($value['goods_list'] as $kk => $vv) {
               $count_goods_num += $vv['goods_num'];
           }
           $order_list[$key]['count_goods_num'] = $count_goods_num;
       }
       
       */
       $this->assign('order_status', C('ORDER_STATUS'));
       $this->assign('shipping_status', C('SHIPPING_STATUS'));
       $this->assign('pay_status', C('PAY_STATUS'));
       $this->assign('page', $show);
       $this->assign('lists', $order_list);
       $this->assign('active', 'order_list');
       $this->assign('active_status', I('get.type'));
       if ($_GET['is_ajax']) {
           return $this->fetch('ajax_order_list');
           exit;
       }
       return $this->fetch();
   }
   
   /*
    * 订单支付页面
    */
   public function cart4(){
       $order_id = I('order_id/d');
       
       $type = I('type');
       if(!$type){
           $type = 'kd';
       }
       
       
       if($this->user_id == 0){
          $url = SITE_URL.'/mobile/kuaidi/cart4/order_id/'.$order_id.'/type/'.$type;
          session('url',$url);
           header("Location: " . "http://www.yykddn.com/codetoany/getcode.php?auk=url" );
          exit;
       }
       
       
       
      
       $order = M('kd_order')->where("order_id", $order_id)->find();
   
       //订单不存在或者订单不是本人
       if(!$order || $order['user_id'] !== session('user.user_id')){
           header("Location: " . "/index.php?m=Mobile&c=User&a=index" );
           exit;
       }
       // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
       if($order['pay_status'] == 1 || $order['pay_code'] == 'cod'){
           $order_detail_url = U("Mobile/kuaidi/order_detail",array('id'=>$order_id));
           header("Location: $order_detail_url");
           exit;
       }
   
       if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
           //微信浏览器
           if($order['order_prom_type'] == 4){
               //预售订单
               $payment_where['code'] = 'weixin';
           }else{
               $payment_where['code'] = array('in',array('weixin','cod'));
           }
       }else{
           if($order['order_prom_type'] == 4){
               //预售订单
               $payment_where['code'] = array('neq','cod');
           }
           $payment_where['scene'] = array('in',array('0','1'));
       }
   
       $payment_where['status'] = array('eq','1');
       $paymentList = M('Plugin')->where($payment_where)->select();
       $paymentList = convert_arr_key($paymentList, 'code');
   
       foreach($paymentList as $key => $val)
       {
           $val['config_value'] = unserialize($val['config_value']);
           if($val['config_value']['is_bank'] == 2)
           {
               $bankCodeList[$val['code']] = unserialize($val['bank_code']);
           }
           //判断当前浏览器显示支付方式
           if(($key == 'weixin' && !is_weixin()) || ($key == 'alipayMobile' && is_weixin())){
               unset($paymentList[$key]);
           }
       }
   
       $bank_img = include APP_PATH.'home/bank.php'; // 银行对应图片
       $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
       $this->assign('paymentList',$paymentList);
       $this->assign('bank_img',$bank_img);
       $this->assign('order',$order);
       $this->assign('type',$type);
       $this->assign('bankCodeList',$bankCodeList);
       $this->assign('pay_date',date('H:i', strtotime("+1 hours")));
       return $this->fetch();
   }
   public function money_pay(){
       
       $id = I('get.id/d');

       if(!$id){
           $this->error('支付超时，请重新下单');
           exit;
       }
        
       $map['order_id'] = $id;
       $map['user_id'] = $this->user_id;
       
       $order_info = M('kd_order')->where($map)->find();
        
       if ($order_info['user_id'] !== $this->user_id) {
           $this->error('该订单不是你的，不能查看信息');
           exit;
       }
        
        
        
       if (!$order_info) {
           $this->error('没有获取到订单信息');
           exit;
       }
       
       if ($order_info['pay_status'] == 1) {
           $this->error('订单已经支付，请勿重复操作');
           exit;
       }
       
       update_pay_status_kd($order_info['order_sn']);
       
       //扣减余额
       accountLog($order_info['user_id'],$order_info['order_amount']*(-1),$order_info['order_amount']*100,'快递代拿余额支付'.$order_info['order_amount'].'元，并赠送'.($order_info['order_amount']*100).'积分');
       
  
       $pay_status = M('kd_order')->where(array('order_id' => $id,'user_id' => $this->user_id))->getField('pay_status');
       
        if($pay_status == 1){
            header("Location: ".U('Mobile/kuaidi/chenggong',array('order_id'=>$id,'type'=>$order_info['type'] )));
            exit;
        }else{
            header("Location: ".U('Mobile/kuaidi/cart4',array('order_id'=>$id,'type'=>'kd' ))); 
        }
       

   }
   
   
   /*
    * 取消订单
    */
   public function cancel_order()
   {
       $order_id = I('get.id/d');
       $user_id = session('user.user_id');
       
      
       
       $order = M('kd_order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->find();
      
       //检查是否存在
       if(empty($order)){
           $data = array('status'=>-1,'msg'=>'订单不存在','result'=>'');
           $this->error($data['msg']);
           exit;
       }
       //检查状态
       if( $order['order_status'] > 0){
           $data = array('status'=>-1,'msg'=>'订单已拿，不允许取消','result'=>'');
           $this->error($data['msg']);
           exit;
       }
        
       
       //检查是否未支付的订单
       if($order['pay_status'] == 1){
           $pay_points = ($order['order_amount']) * (-100) ;
           accountLog($user_id, $order['order_amount'],$pay_points, '取消订单，退款',$distribut_money = 0,$order['order_id'],$order['order_sn']);
 
       }
       
       
       
     
       
       $row = M('kd_order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->save(array('order_status'=>5));
       
      
       
       
       $data['order_id'] = $order_id;
       $data['action_user'] = $user_id;
       $data['action_note'] = '您取消了订单';
       $data['order_status'] = 5;
       $data['log_time'] =date('Y-m-d H:i:s');
       $data['status_desc'] = '用户取消订单';
       M('kd_order_action')->add($data);//订单操作记录
       
      
       
       if(!$row){
           $data = array('status'=>-1,'msg'=>'操作失败','result'=>'');
       }else{
           $data = array('status'=>1,'msg'=>'操作成功','result'=>'');
       }
       
       
      
       if ($data['status'] < 0){
           $this->error($data['msg']);
       }else {
       $this->success($data['msg']);
       }
   }
   /*
    * 强制确认收获订单
    */
   public function qiangzhi_queren_order()
   {
       $order_id = I('get.id/d');
       $user_id = session('user.user_id');
   
        
   
       $order = M('kd_order')->where(array('order_id'=>$order_id))->find();
        
       //检查是否未支付订单 已支付联系客服处理退款
       if(empty($order)){
           $data = array('status'=>-1,'msg'=>'订单不存在','result'=>'');
            $this->error($data['msg']);
            exit;
        }
       //检查是否未支付的订单
       //if( $order['order_status'] !== 8){
          // $data = array('status'=>-1,'msg'=>'订单没完成，不允许确认收货','result'=>'');
           //$this->error($data['msg']);
          // exit;
      // }
   
   
       $row = M('kd_order')->where(array('order_id'=>$order_id))->save(array('order_status'=>7));
   
        
   
       $queren_time = date('Y-m-d H:i:s');
       $data['order_id'] = $order_id;
       $data['action_user'] = $user_id;
       $data['action_note'] = '系统强制确认收获了订单';
       $data['order_status'] = 7;
       $data['log_time'] = $queren_time;
       $data['status_desc'] = '系统强制确认收获了订单';
       M('kd_order_action')->add($data);//订单操作记录
   
        
       $querenlogic = new Push();
       $querenlogic->push_msg_queren($order_id,$queren_time);
   
       $querenlogic->push_msg_qiangzhi_queren($order_id,$queren_time);
   
       if(!$row){
           $data = array('status'=>-1,'msg'=>'操作失败','result'=>'');
       }else{
           $data = array('status'=>1,'msg'=>'操作成功','result'=>'');
       }
   
   
        
       if ($data['status'] < 0){
           $this->error($data['msg']);
       }else {
           $this->success($data['msg']);
       }
   }
   
   /*
    * 确认收获订单
    */
   public function queren_order()
   {
       $order_id = I('get.id/d');
       $user_id = session('user.user_id');
        
   
        
       $order = M('kd_order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->find();
   
       //检查是否未支付订单 已支付联系客服处理退款
       if(empty($order))
           $data = array('status'=>-1,'msg'=>'订单不存在','result'=>'');
       //检查是否未支付的订单
       if( $order['order_status'] !== 6)
           $data = array('status'=>-1,'msg'=>'订单没完成，不允许确认收货','result'=>'');
   
        
        
       $row = M('kd_order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->save(array('order_status'=>7));
        
   
        
       $queren_time = date('Y-m-d H:i:s');
       $data['order_id'] = $order_id;
       $data['action_user'] = $user_id;
       $data['action_note'] = '您确认收获了订单';
       $data['order_status'] = 7;
       $data['log_time'] = $queren_time;
       $data['status_desc'] = '您确认收获了订单';
       M('kd_order_action')->add($data);//订单操作记录

       
       $querenlogic = new Push();
       $querenlogic->push_msg_queren($order_id,$queren_time);
        
     
        
       if(!$row){
           $data = array('status'=>-1,'msg'=>'操作失败','result'=>'');
       }else{
           $data = array('status'=>1,'msg'=>'操作成功','result'=>'');
       }
        
        
   
       if ($data['status'] < 0){
           $this->error($data['msg']);
       }else {
           $this->success($data['msg']);
       }
   }
  
   public function kefu(){
       return $this->fetch();
   }
    public function ajaxGetMore(){
    	$p = I('p/d',1);
    	$favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1")->order('goods_id DESC')->page($p,10)->cache(true,TPSHOP_CACHE_TIME)->select();//首页推荐商品
    	$this->assign('favourite_goods',$favourite_goods);
    	return $this->fetch();
    }
}