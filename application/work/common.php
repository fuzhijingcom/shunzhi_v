<?php
use think\Db;

//总的状态
function get_order_status($order_status) {
	
	switch ($order_status) {
		case 0:
			$order_status = '未拿';
			break;
			
		case 1:
			$order_status = '已拿';
			break;
		case 10:
			$order_status = '取消';
			break;
		case 6:
			$order_status = '已接单，正在派送中';
			break;
		case 7:
			$order_status = '已完成';
			break;
		case 8:
			$order_status = '已派送，待确认';
			break;
		case 5:
			$order_status = '已删除';
			break;
			
		case 9:
			$order_status = '等待发寄件图';
			break;
			
		case 4:
			$order_status = '已接单，等待上门收件';
			break;
		case 10:
			$order_status = '被删除';
			break;
			
		case 11:
			$order_status = '已发单号';
			break;
		case 12:
			$order_status = '投诉中';
			break;
		case 3:
			$order_status = '取消代拿';
			break;
		default:
			$order_status = '状态出错';;
			break;
	}
	return $order_status;
}

function account($user_id){
	return  M('users')->where('user_id',$user_id)->getField('user_money');
}
function get_receiver($order_id){
	 $receiver = M('kd_order')->where('order_id',$order_id)->getField('receiver');
	 return  $name = M('users_qiang') ->where('user_id',$receiver)->getField('name');
}


function get_schoolcount_by_value($value){
    return  M('users')->where('school',$value)->field('school')->count();

}


function get_schoolname_by_value($value){
     return  M('school')->where('value',$value)->getField('name');
 
}

function songda_time($order_id){
	return  M('kd_order_extra')->where('order_id',$order_id)->getField('songda_time');
}

function type($type){

    switch ($type) {

        case '0':
            $title = '待审核';
            break;

        case '1':
            $title = '已通过';
            break;

        case '3':
            $title = '拒绝';
            break;
            
            case '4':
                $title = '禁止';
                break;
        case 'all':
            $title = '全部';
            break;
        default:
            $title = '未知类型';
            break;
    }

    return $title;
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


?>

 

