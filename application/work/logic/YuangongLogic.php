<?php
namespace app\work\logic;
use think\Model;
use think\Db;

class YuangongLogic extends Model
{
 
 
   
    public function push_msg_count($openid,$work,$time,$place,$dao,$count){
        $kuaidi_name = M('kd') ->where('type',$type)->getField('kuaidi_name');
        $order_id = M('kd_order') ->where('order_sn',$order_sn)->getField('order_id');
        
       $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"ls0mSHZE7plAjUQncVDWBUcpQazq2o8knGZzlriH7wc",
            'url'=>"http://www.yykddn.com/work/sign/index",
            'data'=>array(
                'first'=>array(
                    'value'=>"你在（".$time."）的签收状况

签收数量：    ".$count."
",
                    'color'=>"#000099"
                ),
                'keyword1'=>array(
                    'value'=> $work,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>$time,
                    'color'=>"#000000"
                ),
                'keyword3'=>array(
                    'value'=> $place,
                    'color'=>"#8B1A1A"
                ),
                'keyword4'=>array(
                    'value'=>$dao,
                    'color'=>"#FF0000"
                ),
               
    
                'remark'=>array(
                    'value'=>"
    

点击“详情”查看列表",
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
        return $out ;

    }
    

   
 
}