<?php
namespace app\jijian\logic;
use think\Model;
use think\Db;

class JijianLogic extends Model
{


    //判断下单时间，返回true这可以下单，false则不可以下单
    public function check_time_jijian(){
            $hour=(int)date("G");
           /* 
            if($hour == 12){
                $ii=(int)date("i");
                if($ii >= 15){
                    return false;
                }
                 
            }
            
            if($hour == 13){
                return false;
            }
            
            if($hour == 17){
                $i=(int)date("i");
                if($i >= 30){
                  return false;
                }
            }
            
            if($hour >=18){
                 return true;
            }
     */
            return true;
    }
    

    
    

    public function push_msg_all_jijian($openid,$order_sn,$name,$mobile,$sushe){
        $str = mb_substr($name, 0, 1,'utf-8');
        $name = $str.'**';
       $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"dkcYDaTDQ2pxkEHZGseQ0k3IedBb1gOPVbdW2JlaRD0",
            'url'=>"http://www.yykddn.com/qiangdan/jijian/order_list",
            'data'=>array(
                'first'=>array(
                    'value'=>"上门收件 —— 新订单来了
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $order_sn,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $sushe,
                    'color'=>"#FF0000"
                ),
                'keyword3'=>array(
                    'value'=> $name,
                    'color'=>"#8B1A1A"
                ),
    
                 
                'keyword4'=>array(
                    'value'=> '号码抢单后可见',
                    'color'=>"#FF0000"
                ),
    
                'remark'=>array(
                    'value'=>"
    
注意：你已经实名验证，请不要恶意抢单。
    
如需关闭消息提醒，请点击 菜单栏 、推送设置，关闭提醒。
    
点击“详情”，进入抢单大厅。",
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
    
    
    //抢单后提醒下单用户
    public function push_msg_qiang_jijian($order_id){
        $kd_order_model = M('kd_order');
    
        $user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
        $receiver = $kd_order_model ->where('order_id',$order_id)->getField('receiver');
         
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        $id =  M('users_kd') ->where('user_id',$receiver)->getField('user_id');
        $name =  M('users_kd') ->where('user_id',$receiver)->getField('name');
        $mobile =  M('users_kd') ->where('user_id',$receiver)->getField('mobile');
    
    
       $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"ZEzM0XgAbMZ8dGEIgNHl4J5vhJhGEFxj2OBwT2430PA",
            'url'=>"http://www.yykddn.com/mobile/kuaidi/order_detail/id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=> "你好，快递员已接单
 我们将在一小时之内安排上门收件
    
",
                    'color'=>"#DC143C"
                ),
                'keyword1'=>array(
                    'value'=> '默认韵达快递',
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $name.'
 联系电话：'.$mobile,
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
    
如有问题，请点击 菜单栏 、客服，进行投诉。
    
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
    
    //取件后提醒萌萌哒
    public function push_msg_shipping($order_id){
        $kd_order_model = M('kd_order');
        $order_sn = $kd_order_model ->where('order_id',$order_id)->getField('order_sn');
    
        $user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
        $receiver = $kd_order_model ->where('order_id',$order_id)->getField('receiver');
        $consignee = $kd_order_model ->where('order_id',$order_id)->getField('consignee');
        $user_note = $kd_order_model ->where('order_id',$order_id)->getField('user_note');
        $kuaidi_name = $kd_order_model ->where('order_id',$order_id)->getField('kuaidi_name');
    
        $mobile = M('users') ->where('user_id',$receiver)->getField('mobile');
    
       $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> 'oEORvxOGO3tCtI2U8lLpAEGJCGC8',
            'template_id'=>"mIANehC7zvgIQggPsvReFoPqrct-TpHz-kHnE-ERL3M",
            'url'=>"http://www.yykddn.com/mobile/shangjia/jijian_order_detail/id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=> "快递取件完成，等待平台发送单号
",
                    'color'=>"#DC143C"
                ),
                'keyword1'=>array(
                    'value'=> $receiver,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $mobile,
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
寄件人姓名：".$consignee."
收件人信息：".$user_note."
取件人已完成取件，等待萌萌哒处理。
    
点击“详情” 查看订单详情",
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
    
    //发送单号给用户
    public function push_msg_send_dh_to_user($order_id,$message,$money,$danhao){
        $kd_order_model = M('kd_order');
        $order_sn = $kd_order_model ->where('order_id',$order_id)->getField('order_sn');
    
        $user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
        $receiver = $kd_order_model ->where('order_id',$order_id)->getField('receiver');
        $consignee = $kd_order_model ->where('order_id',$order_id)->getField('consignee');
        $shipping_time = $kd_order_model ->where('order_id',$order_id)->getField('shipping_time');
         
        $user_note = $kd_order_model ->where('order_id',$order_id)->getField('user_note');
        $kuaidi_name = $kd_order_model ->where('order_id',$order_id)->getField('kuaidi_name');
    
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
    
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"NIrAwBY0e1Jn9F5Gc2hwWw-MIGRrpvanvlbNP4UlU6U",
            'url'=>"http://www.yykddn.com/mobile/kuaidi/cart4/order_id/".$order_id."/type/ji/",
            'data'=>array(
                'first'=>array(
                    'value'=> "快递面单填写完成，
".$message."
    
",
                    'color'=>"#FF0000"
                ),
                'keyword1'=>array(
                    'value'=> $danhao,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $money,
                    'color'=>"#000000"
                ),
    
                'keyword3'=>array(
                    'value'=> $shipping_time,
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
    
请点击本消息，付款。（采用微信支付）
 点击“详情” 查看订单详情",
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