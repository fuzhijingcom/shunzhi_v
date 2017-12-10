<?php
namespace app\qiangdan\logic;
use think\Model;
use think\Db;

class QiangLogic extends Model
{

    //抢单后提醒下单用户
    public function push_msg_qiang($order_id){
        $first = "已接单
";
        $order_model = M('kd_order');
 
        $order_detail_url = "http://www.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html";

        $order = $order_model ->where('order_id',$order_id)->find();
    
        $user_id = $order['user_id'];
        $receiver = $order['receiver'];
         
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        
        $name =  M('users_qiang') ->where('user_id',$receiver)->getField('name');
        $mobile =  M('users_qiang') ->where('user_id',$receiver)->getField('mobile');
    
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"FX23BNsQMAQbCRVqGlsA7jwguAEl-A7JM1_FoaAXpHQ",
            'url'=>$order_detail_url,
            'data'=>array(
                'first'=>array(
                    'value'=> $first,
                    'color'=>"#DC143C"
                ),
                'keyword1'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $name.'（ID：'.$receiver.'）',
                    'color'=>"#000000"
                ),
                'keyword3'=>array(
                    'value'=> $mobile,
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
如有问题请点击菜单栏[客服咨询]
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
    
    
    //送达后提醒下单用户
    public function push_msg_songda($order_id){
     
         
      $order_detail_url = "http://www.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html";
      $order = M('kd_order') ->where('order_id',$order_id)->find();
      
      $type_name = $order['kuaidi_name'];
   
        $user_id = $order['user_id'];
        $consignee =  $order['consignee'];
        $receiver_id =  $order['receiver'];
    
        $receiver = M('users_qiang')->where('user_id',$receiver_id)->find();
        
        $openid = M('users')->where('user_id',$user_id)->getField('openid');
        
        $receiver_name = $receiver['name'];
        $receiver_mobile = $receiver['mobile'];
      
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"Ez_8dbzeqOTnTYKgZfgAutu_uRpNH-uIV9kof744YhA",
            'url'=>$order_detail_url,
            'data'=>array(
                'first'=>array(
                    'value'=> "您的（".$type_name."快递）已送达

配送员：".$receiver_name."
电话：".$receiver_mobile,
                    'color'=>"#000000"
                ),
                'keyword1'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $consignee,
                    'color'=>"#000000"
                ),
                'keyword3'=>array(
                    'value'=> '已送达',
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
如有问题请联系配送员               
投诉请点击菜单栏[客服咨询]",
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
    
        return $out;
    }
    
}