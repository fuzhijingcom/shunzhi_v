<?php
namespace app\box\logic;
use think\Model;
use think\Db;

class SendLogic extends Model
{


    //快递下单成功

    public function kd_ok($order_id){
        $order = M('kd_order_box') ->where('order_id',$order_id)->find();
        
        $kuaidi_name = $order['kuaidi_name'];
        $order_sn = $order['order_sn'];
        $name = $order['consignee'];
        $mobile = $order['mobile'];
        $sushe = $order['sushe'];
        $user_id = $order['user_id'];
        
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"LLpsGBMTwEPggn28-IQ-OnhqSineiJp_sBpTCJMSOtU",
            'url'=>"http://www.yykddn.com/box/order/detail/order_id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=>"（".$kuaidi_name."快递），一个订单对应一个快递
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $order_sn,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>$kuaidi_name.'快递',
                    'color'=>"#000099"
                ),
                'keyword3'=>array(
                    'value'=> $name,
                    'color'=>"#8B1A1A"
                ),
                'keyword4'=>array(
                    'value'=>$mobile,
                    'color'=>"#FF0000"
                ),
                'keyword5'=>array(
                    'value'=> $sushe,
                    'color'=>"#FF0000"
                ),
    
                'remark'=>array(
                    'value'=>"
    
订单已成功提交！感谢您的支持。
如信息有误，请点击“详情”取消订单，再重新下单。
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
        
        return $out;

    }
    
    
    
    
}