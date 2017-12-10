<?php
namespace app\work\logic;
use think\Model;
use think\Db;

class WorkLogic extends Model
{
 
    /*
     * 找不到，强制取消订单，并退款
     */
    public function cancel_order()
    {
        $order_id = I('get.id/d');
        $user_id = session('user.user_id');
    
         
         
        $order = M('kd_order')->where(array('order_id'=>$order_id))->find();
         
    
        if(empty($order)){
            $data = array('status'=>-1,'msg'=>'订单不存在','result'=>'');
            $this->error($data['msg']);
            exit;
        }
        //检查是否未支付的订单
        if( $order['order_status'] !== 0 ){
            $data = array('status'=>-1,'msg'=>'订单还没抢，不允许退款订单','result'=>'');
            $this->error($data['msg']);
            exit;
        }
    
        //检查是否未支付的订单
        if( $order['pay_status'] !== 1 ){
            $data = array('status'=>-1,'msg'=>'订单还没支付，不允许退款订单','result'=>'');
            $this->error($data['msg']);
            exit;
        }
         
        $cancel_time = date('Y-m-d H:i:s');
        $row = M('kd_order')->where(array('order_id'=>$order_id))->save(array('order_status'=>5,'admin_note'=> '快递找不到，退款时间：'.$cancel_time ));
         
    
    
         
         
        $data['order_id'] = $order_id;
        $data['action_user'] = $user_id;
        $data['action_note'] = '快递找不到';
        $data['order_status'] = 0;
        $data['log_time'] =date('Y-m-d H:i:s');
        $data['status_desc'] = '取消订单，并退款';
        M('kd_order_action')->add($data);//订单操作记录
    
        //   增加余额
        accountLog($order['user_id'],$order['order_amount'],$order['order_amount']*(-100),'快递代拿退款余额增加'.$order['order_amount'].'元，并扣减'.($order['order_amount']*100).'积分');
    
    
        //$data['receiver'] = session('user.user_id');
        // $data['receivetime'] = date('Y-m-d H:i:s');
        //$data['order_status'] = 6;
        // M('kd_order')->where(array('order_id'=>$order_id))->save($data);//订单操作记录
    
    
         
        if(!$row){
            $data = array('status'=>-1,'msg'=>'操作失败','result'=>'');
        }else{
            $data = array('status'=>1,'msg'=>'操作成功','result'=>'');
    
            $logic = new Push();
            $model = 'kuaidi';
            $data = $logic->push_msg_nofind($model,$order_id);
        }
    
        if ($data['status'] < 0){
            $this->error($data['msg']);
        }else {
            $this->success('操作成功！已经退款给客户<br>');
        }
 
    
    }
 
}