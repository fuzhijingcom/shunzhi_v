<?php
namespace app\repair\controller;

use app\home\logic\UsersLogic;
use app\home\model\Message;
use app\common\logic\OrderLogic;
use think\Page;
use think\Request;
use think\Verify;
use think\db;

class Index extends MobileBase
{

    public $user_id = 0;
    public $user = array();

    /*
    * 初始化操作
    */
    public function _initialize()
    {
        parent::_initialize();
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->where("user_id", $user['user_id'])->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
        }
       
    }

    /*
     * 用户中心首页
     */
    public function index()
    {
        $user_id = session('user.user_id');
       if(IS_POST){
           $data['name'] = $name= session('user.nickname');
           $data['mobile'] = $mobile = I('mobile');
           $data['problem'] = $problem = I('problem');
           $data['user_id'] = $user_id;
           
           
           M('repair')->data($data)->save();
           
           
           $content = '【手机维修】名字：'.$name.'，联系方式：'.$mobile.'，问题：'.$problem;
           
           $this->send("1",$content);
           $this->send("29",$content);
           
           $this->msg_super("1",$name,$mobile,$content);
           $this->msg_super("29",$name,$mobile,$content);
           
           
           $this->success('提交成功','index');
       }
       
       
       return $this->fetch();
    }


    public function computer()
    {
    	$user_id = session('user.user_id');
    	if(IS_POST){
    		$data['name'] = $name= session('user.nickname');
    		$data['mobile'] = $mobile = I('mobile');
    		$data['problem'] = $problem = I('problem');
    		$data['user_id'] = $user_id;
    		
    		
    		M('repair')->data($data)->save();
    		
    		
    		$content = '【手机维修】名字：'.$name.'，联系方式：'.$mobile.'，问题：'.$problem;
    		
    		$this->send("1",$content);
    		$this->send("29",$content);
    		
    		$this->msg_super("1",$name,$mobile,$content);
    		$this->msg_super("29",$name,$mobile,$content);
    		
    		
    		$this->success('提交成功','index');
    	}
    	
    	
    	return $this->fetch();
    }
    
    
    private function send($receive,$content)
    {
       
        $user_id = session('user.user_id');
        $url = "http://v.yykddn.com/chat/index/send/receive/".$receive."/send/".$user_id."/content/".$content;
        $json = array(
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
        
       // dump($json);
    }
    

    public function msg_super($receive,$name,$mobile,$content){
    	
    	$openid = M('users') ->where('user_id',$receive)->getField('openid');
    	$access_token = access_token();
    	$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
    	$json = array(
    			'touser'=> $openid,
    			'template_id'=>"i3qyVqZVVTsY6cUwJxI9szLUNZCqb1XLZy6nR0kL_Go",
    			'url'=>"",
    			'data'=>array(
    					'first'=>array(
    							'value'=>"手机维修订单
",
    							'color'=>"#000099"
    					),
    					'keyword1'=>array(
    							'value'=> $name,
    							'color'=>"#000000"
    					),
    					'keyword2'=>array(
    							'value'=>$content,
    							'color'=>"#000099"
    					),
    					'keyword3'=>array(
    							'value'=>'手机维修订单',
    							'color'=>"#000000"
    					),
    					'remark'=>array(
    							'value'=>$mobile,
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
    	return $out ;
    	
    }
    
}

