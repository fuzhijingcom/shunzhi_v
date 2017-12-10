<?php 

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
     
    $consignee = M('user_address') ->where('user_id',$uid)->getField('consignee');
    return $consignee;
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



