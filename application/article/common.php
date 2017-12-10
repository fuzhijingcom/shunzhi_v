<?php 
function get_img_by_uid($uid){
    return M('users')->where('user_id',$uid)->getField('head_pic');
}


?>