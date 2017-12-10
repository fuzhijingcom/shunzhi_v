<?php
namespace app\jijian\logic;
use think\Model;
use think\Db;

class SendLogic extends Model
{

	public function jijian_pay_ok($order_id){
		$order = M('kd_order_ji') ->where('order_id',$order_id)->find();
		
		$order_amount= $order['order_amount'];
		$name = $order['consignee'];
		$mobile = $order['mobile'];
		$address = $order['address'];
		$name2 = $order['name2'];
		$mobile2 = $order['mobile2'];
		$address2 = $order['address2'];
		$user_id = $order['user_id'];
		$order_id= $order['order_id'];
		$openid = M('users') ->where('user_id',$user_id)->getField('openid');
		
		$access_token = access_token();
		$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
		$json = array(
				'touser'=> $openid,
				'template_id'=>"6iwMqrqJkpx7YmK4AzY4Bf1Sz7LLLb9hv_RXLIH8nDU",
				'url'=>"http://www.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html",
				'data'=>array(
						'first'=>array(
								'value'=>"寄件付款完成",
								'color'=>"#000099"
						),
						'orderProductPrice'=>array(
								'value'=> $order_amount.'元',
								'color'=>"#000000"
						),
						'orderProductName'=>array(
								'value'=>'寄快递',
								'color'=>"#000099"
						),
						'orderAddress'=>array(
								'value'=> "
寄件人姓名：".$name2."
寄件人电话：".$mobile2."
寄件人地址：".$address2 ,
								'color'=>"#8B1A1A"
						),
						'orderName'=>array(
								'value'=>$mobile2,
								'color'=>"#000099"
						),
						'remark'=>array(
								'value'=>"
寄件人姓名：".$name."
寄件人电话：".$mobile."
寄件人地址：".$address ,
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
		
		return $out;
		
	}
	

    //寄件下单成功

    public function jijian_ok($order_id){
        $order = M('kd_order_ji') ->where('order_id',$order_id)->find();
        
        $kuaidi_name = $order['kuaidi_name'];
        $name = $order['consignee'];
        $mobile = $order['mobile'];
        $address = $order['address'];
        $name2 = $order['name2'];
        $mobile2 = $order['mobile2'];
        $address2 = $order['address2'];
        $user_id = $order['user_id'];
        $order_id= $order['order_id'];
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"LLpsGBMTwEPggn28-IQ-OnhqSineiJp_sBpTCJMSOtU",
            'url'=>"http://www.yykddn.com/jijian/order/order_detail/id/".$order_id.".html",
            'data'=>array(
                'first'=>array(
                    'value'=>"寄件快递填写完成
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>'寄快递',
                    'color'=>"#000099"
                ),
                'keyword3'=>array(
                    'value'=> $name2,
                    'color'=>"#8B1A1A"
                ),
                'keyword4'=>array(
                    'value'=>$mobile2,
                    'color'=>"#8B1A1A"
                ),
                'keyword5'=>array(
                    'value'=> $address2,
                    'color'=>"#8B1A1A"
                ),
    
                'remark'=>array(
                    'value'=>"
寄件人姓名：".$name."
寄件人电话：".$mobile."
寄件人地址：".$address ,
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
        
        return $out;

    }
    
    
    
    
}