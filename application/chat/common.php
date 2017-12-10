<?php 

function get_heading_by_id($id){ 
   
    $img = M('users')->where('user_id',$id)->getField('head_pic');
    return $img;
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
    
    if(!$name){
        $name = M('users') ->where('user_id',$uid)->getField('nickname');
    }else{
        return $name;
    }

    return $name;
}
function get_last_login_by_uid($uid) {
    
    $last_login = M('users')->where('user_id',$uid)->getField('last_login');
    
    $last_login = friend_date($last_login);
    
    return $last_login;

}





?>



