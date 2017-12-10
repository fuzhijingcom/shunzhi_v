<?php
namespace app\work\logic;
use think\Model;
use think\Db;

class ShenheLogic extends Model
{
 
 

    public function shenhe($is_validated,$uid,$reason){
        $user = M('users_qiang')->where('user_id',$uid)->find();
        $status = type($is_validated);
       
        if($is_validated == "1"){
            $beizhu = "恭喜你，通过审核";
        }
        if($is_validated == "3"){
            $beizhu = "你的申请暂未通过，请重新提交审核";
        }
        if($is_validated == "4"){
            $beizhu = "你的抢单功能已被禁止";
        }
        
        
        $openid = $user['openid'];
        $name = $user['name'];
        
         $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"Nactu2OQ0SoYwSJf21u3D5d4m04y-_osYrWXU0bcaf8",
            'url'=>"http://www.yykddn.com/qiangdan/index/",
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
".$reason,
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