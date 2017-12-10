<?php
namespace app\kuaidi\logic;
use think\Model;
use think\Db;

class KuaidiLogic extends Model
{


    //判断下单时间，返回true这可以下单，false则不可以下单
    public function check_time(){
            $hour=(int)date("G");
            
           // if($hour == 12){
              //  $ii=(int)date("i");
               // if($ii >= 15){
                  //  return false;
                //}
                 
            //}
            
           // if($hour == 13){
               // return false;
           // }
            
           // if($hour == 14){
               // return false;
           // }
            
            
           // if($hour == 18){
               // $i=(int)date("i");
                //if($i >= 30){
                 // return false;
                //}
           // }
            
           // if($hour >=18){
                // return false;
           // }
     
            return true;
    }
    
    
    /**
     * 取消订单 lxl 2017-4-29
     * @param $user_id  用户ID
     * @param $order_id 订单ID
     * @param string $action_note 操作备注
     * @return array
     */
    public function cancel_order($user_id,$order_id,$action_note='您取消了订单'){
        $order = M('kd_order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->find();
        //检查是否未支付订单 已支付联系客服处理退款
        if(empty($order))
            return array('status'=>-1,'msg'=>'订单不存在','result'=>'');
        //检查是否未支付的订单
        if( $order['order_status'] > 0)
            return array('status'=>-1,'msg'=>'订单状态不允许','result'=>'');
        //获取记录表信息
        //$log = M('account_log')->where(array('order_id'=>$order_id))->find();
        //有余额支付的情况
        if($order['order_amount'] > 0 ){
            accountLog($user_id,$order['order_amount'],$order['order_amount'],"订单取消，退回{$order['order_amount']}元,{$order['order_amount']}积分");
        }
    
      //  if($order['coupon_price'] >0){
         //   $res = array('use_time'=>0,'status'=>0,'order_id'=>0);
           // M('coupon_list')->where(array('order_id'=>$order_id,'uid'=>$user_id))->save($res);
       // }
    
        $row = M('kd_order')->where(array('order_id'=>$order_id,'user_id'=>$user_id))->save(array('order_status'=>3));
    
        $data['order_id'] = $order_id;
        $data['action_user'] = 0;
        $data['action_note'] = $action_note;
        $data['order_status'] = 3;
        $data['pay_status'] = $order['pay_status'];
        $data['shipping_status'] = $order['shipping_status'];
        $data['log_time'] = time();
        $data['status_desc'] = '用户取消订单';
        M('kd_order_action')->add($data);//订单操作记录
    
        //取消订单提醒用户
        
        $user_id = $order['user_id'];
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"h8pFtPbiiwmLm78F1SL6YkOkoE97SLANGRDjlHJynm4",
            'url'=>"http://www.yykddn.com/kuaidi/order/detail/order_id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=>"订单已取消，支付金额已退款到账户余额（不是微信钱包），可下次代拿使用
",
                    'color'=>"#000099"
                ),
                'OrderSn'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'OrderStatus'=>array(
                    'value'=>'已取消',
                    'color'=>"#000099"
                ),
               
        
                'remark'=>array(
                    'value'=>"

点击“详情”查看订单",
                    'color'=>"#000099"
                )
            )
        );
        
        $json = json_encode($json);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
        
        
        if(!$row)
            return array('status'=>-1,'msg'=>'操作失败','result'=>'');
        return array('status'=>1,'msg'=>'操作成功','result'=>'');
    
    }
    
}