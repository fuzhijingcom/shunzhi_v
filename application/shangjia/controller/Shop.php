<?php
namespace app\shangjia\controller;
class Shop extends MobileBase {
    
    
    
    
    
    public function index(){       
        $article_id = I('article_id/d',38);
    	$article = D('article')->where("article_id", $article_id)->find();
    	$this->assign('article',$article);
        return $this->fetch();
    }
 
    
    public function order_list(){
       
        $order_id = I('order_id');
        
        echo '<h1>订单号：</h1>'.$order_id;
        
        echo '<h1>暂未开发</h1>';
        
        
    }
    
}