<?php
namespace app\kefu\logic;
use think\Model;
use think\Db;

class KefuLogic extends Model
{
 
 
   
    public function push_msg_guoqi($order_id){
       
        $order = M('kd_order') ->where('order_id',$order_id)->find();
        $consignee = $order['consignee'];
        $add_time = $order['add_time'];
       $kuaidi_name = $order['kuaidi_name'];
       $consignee = $order['consignee'];
       $sushe = $order['sushe'];
       $mobile = $order['mobile'];
        
        
        
        
        $user_id = $order['user_id'];
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        
       $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"lINZGC8iIts3xVZPHYlbuPM2vDFM95-WFjV3nqH5HfM",
            'url'=>"http://www.yykddn.com/kuaidi/order/pingjia/order_id/".$order_id,
            'data'=>array(
                'first'=>array(
                    'value'=>"驿源邀请您对我们的服务进行评价
（如对此订单有疑问请联系客服）
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>$add_time,
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
快递种类：".$kuaidi_name."
收货人：".$consignee."
宿舍：".$sushe."
手机号码：".$mobile."

点击详情,您可以对订单进行评价",
                    'color'=>"#000000"
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
        return $out ;

    }
    

   
 
}