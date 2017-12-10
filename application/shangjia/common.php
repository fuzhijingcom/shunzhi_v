<?php
use think\Db;


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

?>

 

