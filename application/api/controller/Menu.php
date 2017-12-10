<?php
namespace app\api\controller;
use think\Controller;
use think\Session;

class Menu extends Controller {
	/* {
		"type":"view",
		"name":"香港代购化妆品",
		"url":"http://www.yykddn.com/index.php/mobile/Goods/goodsList/id/855.html"
	} */
    
	
	public function index(){
		echo $access_token = access_token();
		
	}
	
	
    public function create(){
        $access_token = access_token();
         
         $json='{
	        "button":[
	         {
				
	           "name":"服务",
	           "sub_button":[
	           {
				"type":"view",
				"name":"代寄代拿下单",
				"url":"http://v.yykddn.com/kuaidi"   
	           },
	           {
				"type":"view",
				"name":"其他服务下单",
				"url":"http://v.yykddn.com/song"   
	           },
             
	           {
				"type":"view",
				"name":"抢单赚钱大厅",
				"url":"http://v.yykddn.com/qiangdan"   
	           },
				{
				"type":"view",
				"name":"数码维修下单",
				"url":"http://v.yykddn.com/repair"   
	           },
                {
				"type":"view",
				"name":"你的个人中心",
				"url":"http://v.yykddn.com/my/user"   
	           }
	           
	           ]
	        },
	         {
	           "name":"淘淘",
				"type":"view",
				"url":"http://wx.quanzijishi.com/circle/obokla42irx"   
	           
	        },
	        
	        {
	           "name":"我们",
	           "sub_button":[
				{
				"type":"view",
				"name":"咨询投诉",
				"url":"http://v.yykddn.com/kefu"   
	           },
	           {
				"type":"view",
				"name":"建议加盟",
				"url":"http://v.yykddn.com/jiameng"   
	           },{
	             "type":"view",
				"name":"在线观影",
				"url":"http://17kyun.com" 
	           }
	        
             ]
	          }
	        ]
	    }';
        
        
       
    
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
    
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
        var_dump($out);
    
    }
}




/* 
 * {
				"type":"view",
				"name":"代拿订单查询（智能柜）",
				"url":"http://www.yykddn.com/box/order/order_list"   
	           },
	           {
				"type":"view",
				"name":"寄快递订单查询",
				"url":"http://www.yykddn.com/jijian/order/order_list"   
	           },
 * 
 * 
 * 
 * 
 * {
				"type":"view",
				"name":"快递代拿（智能柜）",
				"url":"http://www.yykddn.com/box"   
	           },
 * 
 *  {
				"type":"view",
				"name":"外卖点餐",
				"url":"http://www.yykddn.com/waimai"
	           },
	           {
				"type":"view",
				"name":"驿源商城",
				"url":"http://www.yykddn.com/mobile"
	           }
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * $json='{
	        "button":[
	         {
				"type":"view",
				"name":"免费电影观看",
				"url":"http://www.9413520.cn/vippojie/"
	        },
	        {
				"type":"view",
				"name":"电影讨论区",
				"url":"http://www.yudw.com/forum.php?mod=forumdisplay&fid=169"
	        }
    
	  
	        ]
	          }
	        ]
	    }';
	    
	    
	    */