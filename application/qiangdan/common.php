<?php 

function jietu($order_id){
	$img = M('kd_order_extra')->where('order_id',$order_id)->getField('img');
	if($img == NULL){
		return "http://www.yykddn.com/public/jietu/no.png";
	}else {
		return  "http://www.yykddn.com/public/jietu/".$img;
	}
}



function box_num($order_id){
        $order_extra = M('kd_order_extra')->where('order_id',$order_id)->find();
        return  $order_extra['box'];
}

function box_code($order_id){
    $order_extra = M('kd_order_extra')->where('order_id',$order_id)->find();
    return $order_extra['code'];

}
function box_code_img($order_id){
    $order_extra = M('kd_order_extra')->where('order_id',$order_id)->find();
    $code = $order_extra['code'];
    return "http://s.jiathis.com/qrcode.php?url=".$code;

}
function get_heading_by_id($id){ 
    $user_id = M('kd_order_ji')->where('order_id',$id)->getField('user_id');
    $img = M('users')->where('user_id',$user_id)->getField('head_pic');
    return $img;
}

function yongjin($order_id){
    $order = M('kd_order')->where('order_id',$order_id)->find();
    $order_amount = $order['order_amount'];
    
    $type = $order['type'];
    $kouchu = M('kd')->where('type',$type)->getField('money');
    
    $type = $order['type'];
    if($type=='wm'){
        $fen = (int)$order['admin_note'];
        
        $yongjin = (float)$order_amount - ( (float)$kouchu  * $fen);
        
    }else{
        
    $yongjin = (float)$order_amount - (float)$kouchu;
    
    }
    
    return $yongjin;
}

function get_msg_content($order_id){
    $msg_content = M('feedback')->where('order_id',$order_id)->getField('msg_content');
return $msg_content;
}

function get_dong($name) {
     
$str = mb_substr($name, 0, 3,'utf-8');
$name = $str.'**';

return $name;
}
function get_name_by_type($type) {
     
    $kuaidi_name = M('kd') ->where('type',$type)->getField('kuaidi_name');
    return $kuaidi_name;
}

function get_name_by_uid($uid) {
    $name = M('yuangong') ->where('yid',$uid)->getField('name');
    
    if(!$name){
        $name = M('users_qiang') ->where('user_id',$uid)->getField('name');
    }else{
        return $name;
    }
    
    if(!$name){
        $name = M('user_address') ->where('user_id',$uid)->getField('consignee');
    }else{
        return $name;
    }
    
    if(!$name){
        $name = M('users_kd') ->where('user_id',$uid)->getField('name');
    }else{
        return $name;
    }
    
    return $name;
}

function get_renren_name_by_uid($uid) {
     
    $name = M('users_kd') ->where('user_id',$uid)->getField('name');
    return $name;
}

function get_duanhao_by_uid($uid) {
     
    $duanhao = M('user_address') ->where(array('user_id'=>$uid,'is_default'=>'1'))->getField('duanhao');
    return $duanhao;
}
function get_renren_mobile_by_uid($uid) {
     
    $mobile = M('users_kd') ->where('user_id',$uid)->getField('mobile');
    return $mobile;
}

function get_img_by_type($type) {
     
    $kuaidi_img = M('kd') ->where('type',$type)->getField('kuaidi_img');
    return $kuaidi_img;
}
//快递状态
function status($order_status) {
     
    switch ($order_status) {
        case 0:
        $order_status = '未拿';
        break;
        
        case 1:
        $order_status = '已拿';
       break;
       
       case 3:
       $order_status = '已取消';
      break;
  
        default:
             $order_status = '状态出错';;
        break;
    }
    return $order_status;
}


?>



