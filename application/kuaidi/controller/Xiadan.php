<?php
namespace app\kuaidi\controller;
use app\home\logic\UsersLogic;
use app\kuaidi\logic\KuaidiLogic;
use Think\Db;
class Xiadan extends MobileBase {
    
    public function _initialize(){
        parent ::_initialize();
        $sitting = M('kd_sitting')->where('name','kuaidi')->find();
        $value = $sitting['value'];
        if($sitting['status'] == 0 ){
            $this->redirect('kuaidi/index/errortime', array('errmsg' => $value), 2 , '页面跳转中...');
        }
    }
    
    
    public function xiadan()
    {
        $user_id = session('user.user_id');
        $user_name = session('user.nickname');
        $type = I('type');
        if(!$type){
            $this->error('快递种类错误');
        }
       
        if($type == 'box'){
            $this->redirect('box/xiadan/xiadan');
            exit;
        }
        
        if($type == 'ss'){
            $this->redirect('song/xiadan/xiadan');
            exit;
        }
        
        if($type == 'ji'){
        	$this->redirect('jijian/index/index');
        	exit;
        }
        
        $logic = new KuaidiLogic();
        $time = $logic->check_time();
        if($time==false){
            $this->redirect('kuaidi/index/errortime', array('source' => 'kuaidi'), 1, '页面跳转中...');
        }
    
        if($user_id == 0){
            $this->error('请先登陆');
        }
        $address_id = I('address_id/d');
        if($address_id)
            $address = M('user_address')->where("address_id", $address_id)->find();
        else
            $address = M('user_address')->where(['user_id'=>$user_id,'is_default'=>1])->find();
    
        if(empty($address)){
            header("Location: ".U('Mobile/User/add_address',array('source'=>'kd','type'=>$type)));
        }else{
            $this->assign('address',$address);
        }
    
        $kd_model = M('kd');
        $kuaidi = $kd_model->where("type", $type)->find();
         
        $this->assign('qiang', $kuaidi['qiang'] );
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

        $kuaidi_name = I("kuaidi_name");
        $order_amount = I("order_amount");
       
        $type  = I("type");
        $qiang = M('kd')->where("type", $type)->getField('qiang');
        
        $address = M('UserAddress')->where("address_id", $address_id)->find();
    
        if($address['sushe']==null){
            $this->error('宿舍不能为空，请重新编辑地址');
        }
        
        $code = I('code');//货号
        if($type == 'yt' && $code == null){
            $this->error('货号不能为空，请复制通知短信');
        }
        if($type == 'yz' && $code == null){
            $this->error('货号不能为空，请复制通知短信');
        }
        if($qiang == 1){
            $discount = I("discount");
            $order_amount = (float)$order_amount + (float)$discount;//调整价格
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
            'duanhao'        =>$address['duanhao'],//'短号',
            'type'    =>           $type, //'物流编号',
            'kuaidi_name'    =>    $kuaidi_name, //'快递名称',                为照顾新手开发者们能看懂代码，此处每个字段加于详细注释
            'order_amount'     => $order_amount,
            'add_time'         =>date('Y-m-d H:i:s'), // 下单时间
            'user_note'        => $code.$user_note, // 用户下单备注，加上货号
            'qiang'        =>   $qiang, // 是否抢单
            'discount'        =>$discount, // 加价多少
            'school'        =>$school, // 哪个学校的
            
        );
    
        $order_id = M("kd_order")->insertGetId($data);
    
        $action_info = array(
            'order_id'        =>$order_id,
            'action_user'     =>$user_id,
            'action_note'     => '您提交了订单，未拿',
            'status_desc'     =>'提交订单', //''
            'log_time'        =>date('Y-m-d H:i:s'),
        );
        M('kd_order_action')->insertGetId($action_info);
    
        //如果是圆通
        if($type == 'yt'){
            $extra = M('kd_order_extra')->where(array('order_id'=>$order_id))->find();
            $extra_data['order_id'] = $order_id;
            $extra_data['code'] = $code;
            if(!$extra){
                M('kd_order_extra')->save($extra_data);
            }else{
                M('kd_order_extra')->where(array('order_id'=>$order_id))->save($extra_data);
            }
        }
        
        
        //上传截图
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        if($file){
	        	// 移动到框架应用根目录/public/uploads/ 目录下
	        	if($file){
	        		$info = $file->move(ROOT_PATH . 'public' . DS . 'jietu');
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
	        			//$this->error('照片上传出错');
	        		}
	        	}
	        	//if(!$imgurl){
	        		//$this->error('照片上传出错,请重新上传');
	        	//}
	        	
	        	$extra1 = M('kd_order_extra')->where(array('order_id'=>$order_id))->find();
	        	$extra_img['order_id'] = $order_id;
	        	$extra_img['img'] = $imgurl;
	        	if(!$extra1){
	        		M('kd_order_extra')->save($extra_img);
	        	}else{
	        		M('kd_order_extra')->where(array('order_id'=>$order_id))->save($extra_img);
	        	}
	        	
        }
        
        header("Location: ".U('pay/payment/kuaidi',array('order_id'=>$order_id,'source'=>'kuaidi' )));
       
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