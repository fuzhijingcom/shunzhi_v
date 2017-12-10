<?php
namespace app\work\logic;
use think\Model;
use think\Db;

class WorkLogic extends Model
{
 
 
   /*
    public function push_msg_ok($openid,$type,$order_sn,$name,$mobile,$sushe){
        $kuaidi_name = M('kd') ->where('type',$type)->getField('kuaidi_name');
        $order_id = M('kd_order') ->where('order_sn',$order_sn)->getField('order_id');
        
       $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"LLpsGBMTwEPggn28-IQ-OnhqSineiJp_sBpTCJMSOtU",
            'url'=>"http://www.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html",
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

    }
    */

    public function push_msg_yina($order_id,$uid,$receivetime,$beizhu = ''){
        $kd_order_model = M('kd_order');
        $order_sn = $kd_order_model ->where('order_id',$order_id)->getField('order_sn');
        $user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
        $consignee = $kd_order_model ->where('order_id',$order_id)->getField('consignee');
        $kuaidi_name = $kd_order_model ->where('order_id',$order_id)->getField('kuaidi_name');
        
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        
         $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"h8pFtPbiiwmLm78F1SL6YkOkoE97SLANGRDjlHJynm4",
            'url'=>"http://www.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=> "亲爱的".$consignee."
您的快递已被我们签收
签收时间：".$receivetime."
签收者ID：".$uid."

".$beizhu."
",
                    'color'=>"#DC143C"
                ),
                'OrderSn'=>array(
                    'value'=> $order_sn,
                    'color'=>"#000000"
                ),
                'OrderStatus'=>array(
                    'value'=>'已拿 （'.$kuaidi_name.'快递）',
                    'color'=>"#000000"
                ),

                'remark'=>array(
                    'value'=>"
派送到宿舍时间为：晚上7,8,9,10点                  
请不要催，如没收到请联系客服
                    
点击“详情”查看完整订单信息",
                    'color'=>"#8B1A1A"
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
    public function push_msg_yina_box($order_id,$uid,$receivetime){
        $kd_order_model = M('kd_order_box');
        $order_sn = $kd_order_model ->where('order_id',$order_id)->getField('order_sn');
        $user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
        $consignee = $kd_order_model ->where('order_id',$order_id)->getField('consignee');
        $kuaidi_name = $kd_order_model ->where('order_id',$order_id)->getField('kuaidi_name');
    
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
    
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"h8pFtPbiiwmLm78F1SL6YkOkoE97SLANGRDjlHJynm4",
            'url'=>"http://www.yykddn.com/box/order/order_detail/id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=> "亲爱的".$consignee."
您的快递已被我们签收
签收时间：".$receivetime."
签收者ID：".$uid."
",
                    'color'=>"#DC143C"
                ),
                'OrderSn'=>array(
                    'value'=> $order_sn,
                    'color'=>"#000000"
                ),
                'OrderStatus'=>array(
                    'value'=>'已拿 （'.$kuaidi_name.'快递）',
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
    
点击“详情”查看完整订单信息",
                    'color'=>"#8B1A1A"
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
    
    
    /*
     * 向用户推送消息
     */
    public function push_msg($openid,$content){
       $access_token = access_token();
        $url ="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";        
        $post_arr = array(
                        'touser'=>$openid,
                        'msgtype'=>'text',
                        'text'=>array(
                                'content'=>$content,
                            )
                        );
        $post_str = json_encode($post_arr,JSON_UNESCAPED_UNICODE);        
        $return = httpRequest($url,'POST',$post_str);
        $return = json_decode($return,true);        
    }
 
}