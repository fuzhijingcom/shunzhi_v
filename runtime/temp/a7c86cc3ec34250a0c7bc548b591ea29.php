<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:33:"./template/work/jijian/index.html";i:1511276473;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>寄件后台</title>
    <link rel="stylesheet" href="__PUBLIC__/css/weui.min.css"/>
<style type="text/css">
body{background-color:#efeff4;-webkit-tap-highlight-color:transparent;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none
}
.container {
padding-bottom: 20px
}
.weui-cells_checkbox>.weui-cell>* {
pointer-events: none
}
.list__textarea-label_1IUds {
float: left;
color: #222;
margin-bottom: 10px
}
.list__no-arrow_1QzDq:after {
display: none!important
}
.list__warn_qnkn9 {
color: #e64340!important
}
a.weui-cell {
color: #222
}
.weui-cell__ft {
font-size: 0
}
.zh_CN .weui-cell__ft,.zh_HK .weui-cell__ft,.zh_TW .weui-cell__ft {
font-size: 17px}
.comment__textarea-label_3n9Pv{float:left;color:#222;margin-bottom:10px}
</style>
</head>
<body>
   
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title">寄件说明</h2>
        <p class="weui-msg__desc">
可以看到用户的寄件地址等信息，然后照着这些信息支付宝下单
<br>
在后台把单号、付款金额，发给客户
<br>
客户在线支付后，会收到已付款通知。
<br>
服务费再根据重量当场给抢单员
<br>
用户付款1元。抢单员线上的佣金是1元<br>
再根据重量情况追加服务费，在店里用现金或其他方式支付</p>
    </div>
    <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
        	<a href="/work/jijian/read?order_status=6" class="weui-btn weui-btn_default" >在路上（<?php echo $count6; ?>个）</a>
            <a href="/work/jijian/read?order_status=7" class="weui-btn weui-btn_primary" >待寄件（<?php echo $count7; ?>个）</a>
            <a href="/work/jijian/read?order_status=8&pay_status=0" class="weui-btn weui-btn_warn" >待付款（<?php echo $count8; ?>个）</a>
            <a href="/work/jijian/read?order_status=8&pay_status=1" class="weui-btn weui-btn_default" >已付款列表</a>
        </p>
    </div>

</body></html>