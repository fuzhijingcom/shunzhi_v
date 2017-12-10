<?php
namespace app\work\logic;
use think\Model;
use think\Db;

class QiangLogic extends Model
{
    public function change($id,$openid,$name,$status,$beizhu){
         $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"d4QWkryD4pE2JEf-nX8fcYNCB-gpDRXVLX8kB5FLC8c",
            'url'=>"http://www.yykddn.com/kuaidi/order/order_detail/id/".$id,
            'data'=>array(
                'first'=>array(
                    'value'=> $beizhu."
",
                    'color'=>"#DC143C"
                ),
                'keyword1'=>array(
                    'value'=> $name,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>$status,
                    'color'=>"#DC143C"
                ),
                'keyword3'=>array(
                    'value'=>date('Y-m-d H:i:s'),
                    'color'=>"#000000"
                ),
                

                'remark'=>array(
                    'value'=>"
点击[详情]，查看订单详情",
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
    
    }
   
    public function change_receiver($id,$openid,$name,$status,$beizhu){
    	$access_token = access_token();
    	$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
    	$json = array(
    			'touser'=> $openid,
    			'template_id'=>"d4QWkryD4pE2JEf-nX8fcYNCB-gpDRXVLX8kB5FLC8c",
    			'url'=>"",
    			'data'=>array(
    					'first'=>array(
    							'value'=> "你的申请客服已通过。
申请取消的订单编号：".$id."

",
    							'color'=>"#DC143C"
    					),
    					'keyword1'=>array(
    							'value'=> $name,
    							'color'=>"#000000"
    					),
    					'keyword2'=>array(
    							'value'=>date('Y-m-d H:i:s'),
    							'color'=>"#000000"
    					),
    					'keyword3'=>array(
    							'value'=>"状态更改结果：".$status,
    							'color'=>"#DC143C"
    					),
    					
    					'remark'=>array(
    							'value'=>"
备注：
".$beizhu,
    							'color'=>"#DC143C"
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