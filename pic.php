<?php

$url = "http://v.yykddn.com/api/menu";

$ch = curl_init ();
curl_setopt ( $ch, CURLOPT_URL, $url );
curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
$access_token= curl_exec ( $ch );
curl_close ( $ch );
//$jsoninfo = json_decode ( $output, true );
//$access_token = $jsoninfo ["access_token"];


$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";
$data =array("media" => "@"."daiqu.png");


$result = https_request ( $url, $data );
var_dump ( $result );


function https_request($url, $data = null) {
$curl = curl_init ();
//curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, false); 
curl_setopt ( $curl, CURLOPT_URL, $url );
curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
if (! empty ( $data )) {
curl_setopt ( $curl, CURLOPT_POST, 1 );
curl_setopt ( $curl, CURLOPT_POSTFIELDS, $data );
}
curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
$output = curl_exec ( $curl );
curl_close ( $curl );
return $output;
}

?>