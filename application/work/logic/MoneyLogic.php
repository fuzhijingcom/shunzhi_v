<?php
namespace app\work\logic;
use think\Model;
use think\Db;

class MoneyLogic extends Model
{
 
 

    public function tixian($user_id,$id,$kefu,$beizhu,$status){
        $openid = M('users')->where('user_id',$user_id)->getField('openid');

        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"d4QWkryD4pE2JEf-nX8fcYNCB-gpDRXVLX8kB5FLC8c",
            'url'=>"http://www.yykddn.com/my/user/account/",
            'data'=>array(
                'first'=>array(
                    'value'=> "您的提现问题已经有了新的处理
",
                    'color'=>"#DC143C"
                ),
                'keyword1'=>array(
                    'value'=> $kefu,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=>date('Y-m-d H:i:s'),
                    'color'=>"#DC143C"
                ),
                'keyword3'=>array(
                    'value'=>$beizhu,
                    'color'=>"#000000"
                ),
                

                'remark'=>array(
                    'value'=>"
感谢您的支持，您的支持是我们前进的动力",
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
   
    
   
 
}