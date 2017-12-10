<?php
namespace app\mobile\logic;
use think\Model;
use think\Db;

class SendLogic extends Model
{


    //快递下单成功

    public function ok($order_id,$openid,$order_sn,$name,$mobile,$sushe,$goods_name,$goods_num){
        
        
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"LLpsGBMTwEPggn28-IQ-OnhqSineiJp_sBpTCJMSOtU",
            'url'=>"http://www.yykddn.com/mobile/user/order_detail/id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=>"下单成功！订单号：".$order_id."
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $order_sn,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>'
商品名称：'.$goods_name.'等
订单数量：'.$goods_num,
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
    
订单已成功提交！
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
    
    

    public function new_order($order_id,$openid,$order_sn,$name,$mobile,$sushe,$goods_name,$goods_num,$order_amount){
       $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"ktU5NKT6ESgBykMZzWyEJNQS5rCJZkwkuRmtcSVRSbs",
            'url'=>"http://www.yykddn.com/shangjia/shop/order_list/order_id/".$order_id,
            'data'=>array(
                'first'=>array(
                    'value'=>"商城新订单 —— 新订单来了
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $name,
                    'color'=>"#FF0000"
                ),
                'keyword3'=>array(
                    'value'=> $order_amount,
                    'color'=>"#8B1A1A"
                ),
                'keyword4'=>array(
                    'value'=> '商品名称：'.$goods_name.'等
订单数量：'.$goods_num.'
手机：'.$mobile.'
宿舍：'.$sushe,
                 
                    'color'=>"#FF0000"
                ),
                'keyword5'=>array(
                    'value'=> '无',
                    'color'=>"#FF0000"
                ),
                'remark'=>array(
                    'value'=>"
    

赶紧配送
点击“详情”，进入订单列表。",
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
    
    }
    
}