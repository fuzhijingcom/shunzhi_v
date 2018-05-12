<?php
namespace app\jijian\controller;
use app\jijian\logic\JijianLogic;
use think\Db;
use think\Page;

class Order extends MobileBase {
    public function index(){
        $user_id = session('user.user_id');
        if(!user_id){
            $this->error('请先登录');
            exit;
        }
        $this->redirect('order_list');
    }
  
   
     /*
     * 订单详情
     */
    public function order_detail()
    {
        $user_id = session('user.user_id');
        $id = I('get.id/d');
        $map['order_id'] = $id;
        $map['user_id'] = $user_id;
        $order_info = M('kd_order_ji')->where($map)->find();
        $order_info = set_btn_order_status($order_info);  // 添加属性  包括按钮显示属性 和 订单状态显示属性
        if (!$order_info) {
            $this->error('没有获取到订单信息');
            exit;
        }
       
       
        
        $region_list = get_region_list();
        $invoice_no = M('DeliveryDoc')->where("order_id", $id)->getField('invoice_no', true);
        $order_info[invoice_no] = implode(' , ', $invoice_no);
        //获取订单操作记录
        $order_action = M('kd_order_action_ji')->where(array('order_id' => $id))->select();
        $this->assign('order_status', C('ORDER_STATUS'));
        $this->assign('shipping_status', C('SHIPPING_STATUS'));
        $this->assign('pay_status', C('PAY_STATUS'));
        $this->assign('region_list', $region_list);
        $this->assign('order_info', $order_info);
        $this->assign('order_action', $order_action);

        if (I('waitreceive')) {  //待收货详情
            return $this->fetch('wait_receive_detail');
        }
        return $this->fetch();
    }

     
    
    /*
     * 订单列表
     */
    public function order_list()
    {
        $user_id = session('user.user_id');
        $where['user_id'] = $user_id;
       
        //条件搜索
        $order_status = I('get.order_status');
        if($order_status!==''){
            $where['order_status'] = $order_status;;
        }
        $count = M('kd_order_ji')->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $order_str = "order_id DESC";
        $order_list = M('kd_order_ji')->order($order_str)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
    
     
        
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
     * 取消订单
     */
    public function cancel_order()
    {
        $user_id = session('user.user_id');
        $id = I('get.id/d');
        //检查是否有积分，余额支付
        $logic = new JijianLogic();
        $data = $logic->cancel_order($user_id, $id);
        return $this->ajaxReturn($data);
        
    }
    
  
     
    
    
}