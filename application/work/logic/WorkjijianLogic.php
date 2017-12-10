<?php
namespace app\work\logic;
use think\Model;
use think\Db;

class WorkjijianLogic extends Model
{
 
	//       header("Location: ".U('pay/payment/kuaidi',array('order_id'=>$order_id,'source'=>'kuaidi' )));
	//发送单号给用户
	public function push_msg_send_dh_to_user($order_id,$admin_note,$money){
		$kd_order_model = M('kd_order_ji');
		
		$user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
		$shipping_time = $kd_order_model ->where('order_id',$order_id)->getField('shipping_time');
		
		$openid = M('users') ->where('user_id',$user_id)->getField('openid');
		
		$access_token = access_token();
		$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
		$json = array(
				'touser'=> $openid,
				'template_id'=>"NIrAwBY0e1Jn9F5Gc2hwWw-MIGRrpvanvlbNP4UlU6U",
				'url'=>"http://www.yykddn.com/pay/payment/jijian/order_id/".$order_id."/type/ji/",
				'data'=>array(
						'first'=>array(
								'value'=> "快递面单填写完成，
".$admin_note."
								
",
								'color'=>"#FF0000"
						),
						'keyword1'=>array(
								'value'=> $admin_note,
								'color'=>"#000000"
						),
						'keyword2'=>array(
								'value'=> $money.'元',
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
		return $out;
		
	}
	
	
	
	
 
}