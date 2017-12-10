<?php
use think\Db;

function get_head_pic_by_uid($uid) {
     
     return  M('users') ->where(array('user_id'=>$uid))->getField('head_pic');

}


?>