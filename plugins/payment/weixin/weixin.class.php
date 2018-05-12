<?php
use think\Model; 
/**
 * 支付 逻辑定义
 * Class 
 * @package Home\Payment
 */

class weixin extends Model
{    
    public $tableName = 'plugin'; // 插件表        
    public $alipay_config = array();// 支付宝支付配置参数
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();
                
        require_once("lib/WxPay.Api.php"); // 微信扫码支付demo 中的文件         
        require_once("example/WxPay.NativePay.php");
        require_once("example/WxPay.JsApiPay.php");

		//$paymentPlugin = M('Plugin')->where("code='weixin' and  type = 'payment' ")->find(); // 找到微信支付插件的配置
		
		$config_value = array(
			'appid' => 'wxe6521d177830148a', // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
			'mchid'=> '1501764661', // * MCHID：商户号（必须配置，开户邮件中可查看）
			'key' => 'BZPMP0BbXBXWvHg1zsxIF0YnNRBY8lSO',
			'appsecret' => '4322bf3ec27f97d84169ace2d7545b09'
		);

		//$config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化  

        WxPayConfig::$appid = $config_value['appid']; // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        WxPayConfig::$mchid = $config_value['mchid']; // * MCHID：商户号（必须配置，开户邮件中可查看）
        WxPayConfig::$smchid = isset($config_value['smchid']) ? $config_value['smchid'] : ''; // * SMCHID：服务商商户号（必须配置，开户邮件中可查看）
        WxPayConfig::$key = $config_value['key']; // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        WxPayConfig::$appsecret = $config_value['appsecret']; // 公众帐号secert（仅JSAPI支付的时候需要配置)，                                      
    }    
    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */

    // function get_code($order, $config_value)
    // {       
    //         $notify_url = SITE_URL.'/index.php/Home/Payment/notifyUrl/pay_code/weixin'; // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
    //         //$notify_url = C('site_url').U('Home/Payment/notifyUrl',array('pay_code'=>'weixin')); // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
    //         //$notify_url = C('site_url')."/index.php?m=Home&c=Payment&a=notifyUrl&pay_code=weixin";
    //         $input = new WxPayUnifiedOrder();
    //         $input->SetBody("TPshop商品"); // 商品描述
    //         $input->SetAttach("weixin"); // 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
    //         $input->SetOut_trade_no($order['order_sn'].time()); // 商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
    //         $input->SetTotal_fee($order['order_amount']*100); // 订单总金额，单位为分，详见支付金额
    //         $input->SetNotify_url($notify_url); // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
    //         $input->SetTrade_type("NATIVE"); // 交易类型   取值如下：JSAPI，NATIVE，APP，详细说明见参数规定    NATIVE--原生扫码支付
    //         $input->SetProduct_id("123456789"); // 商品ID trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
    //         $notify = new NativePay();
    //         $result = $notify->GetPayUrl($input); // 获取生成二维码的地址
    //         $url2 = $result["code_url"];
    //         return '<img alt="模式二扫码支付" src="/index.php?m=Home&c=Index&a=qr_code&data='.urlencode($url2).'" style="width:110px;height:110px;"/>';        
	// }    
	
    /**
     * 服务器点对点响应操作给支付接口方调用
     * 
     */
    function response()
    {   
        $order_id = I('order_id');
        $source = I('source');
        
        //不同的来源调用不同的
        if($source=='sz'){
            require_once("example/notify_sz.php");  
            $notify = new PayNotifyCallBack();
            $notify->Handle(false);  
        }
        
        //不同的来源调用不同的
        if($source=='box'){
            require_once("example/notify_box.php");
            $notify = new PayNotifyCallBack();
            $notify->Handle(false);
        }
        
        //不同的来源调用不同的
        if($source=='ji'){
        	require_once("example/notify_ji.php");
        	$notify = new PayNotifyCallBack();
        	$notify->Handle(false);
        }
        
        require_once("example/notify.php");
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }
    
    
    function getpay($order_id,$source){
		
        if($source=='sz'){
            $go_url = U('kuaidi/send/check',array('order_id'=>$order_id,'source'=>$source));
            $back_url = U('pay/payment/kuaidi',array('order_id'=>$order_id,'source'=>$source));
            $order = M('kd_order')->where('order_id',$order_id)->find();
            $body= "支付(".$order['consignee'].")快递代拿订单：".$order['order_sn'];
            $Notify_url = SITE_URL.'/pay/payment/notifyUrl/source/'.$source.'/oeder_id/'.$order_id.'/pay_code/weixin';
        }
        elseif($source=='box'){
            $go_url = U('box/send/check',array('order_id'=>$order_id,'source'=>$source));
            $back_url = U('pay/payment/box',array('order_id'=>$order_id,'source'=>$source));
            $order = M('kd_order_box')->where('order_id',$order_id)->find();
            $body= "支付(".$order['consignee'].")代拿订单：".$order['order_sn'];
            $Notify_url = SITE_URL.'/pay/payment/notifyUrl/source/'.$source.'/order_id/'.$order_id.'/pay_code/weixin';
        }elseif($source=='ji'){
        	$go_url = U('jijian/send/check',array('order_id'=>$order_id,'source'=>$source));
        	$back_url = U('pay/payment/jijian',array('order_id'=>$order_id,'source'=>$source));
        	$order = M('kd_order_ji')->where('order_id',$order_id)->find();
        	$body= "支付(".$order['consignee'].")寄件订单：".$order['order_id'];
        	$Notify_url = SITE_URL.'/pay/payment/notifyUrl/source/'.$source.'/order_id/'.$order_id.'/pay_code/weixin';
        }
        
       else{
           $this->error('支付出错，getpay错误，请联系我们');
		}
		
        //①、获取用户openid
        $tools = new JsApiPay();
        //$openId = $tools->GetOpenid();
        //$openId = $_SESSION['openid'];
        $openId = session('user.openid_ch');
		//②、统一下单
		
        $input = new WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach("weixin");
        $input->SetOut_trade_no($order['order_sn'].time());
        $input->SetTotal_fee($order['order_amount']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("tp_wx_pay");
        $input->SetNotify_url($Notify_url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order2 = WxPayApi::unifiedOrder($input);
        //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        //printf_info($order);exit;
        $jsApiParameters = $tools->GetJsApiParameters($order2);
        $html = <<<EOF
	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',$jsApiParameters,
			function(res){
				//WeixinJSBridge.log(res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok") {
				    location.href='$go_url';
				 }else{
				 	alert(res.err_code+res.err_desc+res.err_msg);
				 	//alert("您取消了支付，请重新支付");
				    location.href='$back_url';
				 }
			}
		);
	}
    
	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	callpay();
	</script>
EOF;
    
        return $html;
    
    }
    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        // 微信扫码支付这里没有页面返回
    }


	//这是商城自带  的   支付


    function getJSAPI($order){
    	if(stripos($order['order_sn'],'recharge') !== false){
    		$go_url = U('Mobile/User/points',array('type'=>'recharge'));
    		$back_url = U('Mobile/User/recharge',array('order_id'=>$order['order_id']));
    	}else{
    		$go_url = U('Mobile/Send/order',array('id'=>$order['order_id']));
    		$back_url = U('Mobile/Cart/cart4',array('order_id'=>$order['order_id']));
    	}
        //①、获取用户openid
        $tools = new JsApiPay();
        //$openId = $tools->GetOpenid();
        $openId = $_SESSION['openid'];
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("支付订单：".$order['order_sn']);
        $input->SetAttach("weixin");
        $input->SetOut_trade_no($order['order_sn'].time());
        $input->SetTotal_fee($order['order_amount']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("tp_wx_pay");
        $input->SetNotify_url(SITE_URL.'/index.php/Home/Payment/notifyUrl/pay_code/weixin');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order2 = WxPayApi::unifiedOrder($input);
        //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        //printf_info($order);exit;  
        $jsApiParameters = $tools->GetJsApiParameters($order2);
        $html = <<<EOF
	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',$jsApiParameters,
			function(res){
				//WeixinJSBridge.log(res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok") {
				    location.href='$go_url';
				 }else{
				 	//alert(res.err_code+res.err_desc+res.err_msg);
				    location.href='$back_url';
				 }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	callpay();
	</script>
EOF;
        
    return $html;

    }
    
    function transfer($data){
    	//CA证书及支付信息
    	$wxchat['appid'] = WxPayConfig::$appid;
    	$wxchat['mchid'] = WxPayConfig::$smchid;
    	$wxchat['api_cert'] = './plugins/payment/weixin/cert/apiclient_cert.pem';
    	$wxchat['api_key'] = './plugins/payment/weixin/cert/apiclient_key.pem';
    	$wxchat['api_ca'] = './plugins/payment/weixin/cert/rootca.pem';
    	$webdata = array(
    			'mch_appid' => $wxchat['appid'],
    			'mchid'     => $wxchat['smchid'],
    			'nonce_str' => md5(time()),
    			//'device_info' => '1000',
    			'partner_trade_no'=> $data['pay_code'], //商户订单号，需要唯一
    			'openid' => $data['openid'],//转账用户的openid
    			'check_name'=> 'NO_CHECK', //OPTION_CHECK不强制校验真实姓名, FORCE_CHECK：强制 NO_CHECK：
    			//'re_user_name' => 'jorsh', //收款人用户姓名
    			'amount' => $data['money'] * 100, //付款金额单位为分
    			'desc'   => empty($data['desc'])? '退款' : $data['desc'],
    			'spbill_create_ip' => request()->ip(),
    	);
    	foreach ($webdata as $k => $v) {
    		$tarr[] =$k.'='.$v;
    	}
    	sort($tarr);
    	$sign = implode($tarr, '&');
    	$sign .= '&key='.WxPayConfig::$key;
    	$webdata['sign']=strtoupper(md5($sign));
    	$wget = $this->array2xml($webdata);
    	$pay_url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    	$res = $this->http_post($pay_url, $wget, $wxchat);
    	if(!$res){
    		return array('status'=>1, 'msg'=>"Can't connect the server" );
    	}
    	$content = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
    	if(strval($content->return_code) == 'FAIL'){
    		return array('status'=>1, 'msg'=>strval($content->return_msg));
    	}
    	if(strval($content->result_code) == 'FAIL'){
    		return array('status'=>1, 'msg'=>strval($content->err_code),':'.strval($content->err_code_des));
    	}
    	$rdata = array(
    			'mch_appid'        => strval($content->mch_appid),
    			'mchid'            => strval($content->mchid),
    			'device_info'      => strval($content->device_info),
    			'nonce_str'        => strval($content->nonce_str),
    			'result_code'      => strval($content->result_code),
    			'partner_trade_no' => strval($content->partner_trade_no),
    			'payment_no'       => strval($content->payment_no),
    			'payment_time'     => strval($content->payment_time),
    	);
    	return $rdata;
    }
    
    /**
     * 将一个数组转换为 XML 结构的字符串
     * @param array $arr 要转换的数组
     * @param int $level 节点层级, 1 为 Root.
     * @return string XML 结构的字符串
     */
    function array2xml($arr, $level = 1) {
    	$s = $level == 1 ? "<xml>" : '';
    	foreach($arr as $tagname => $value) {
    		if (is_numeric($tagname)) {
    			$tagname = $value['TagName'];
    			unset($value['TagName']);
    		}
    		if(!is_array($value)) {
    			$s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
    		} else {
    			$s .= "<{$tagname}>" . $this->array2xml($value, $level + 1)."</{$tagname}>";
    		}
    	}
    	$s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
    	return $level == 1 ? $s."</xml>" : $s;
    }
    
    function http_post($url, $param, $wxchat) {
    	$oCurl = curl_init();
    	if (stripos($url, "https://") !== FALSE) {
    		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
    	}
    	if (is_string($param)) {
    		$strPOST = $param;
    	} else {
    		$aPOST = array();
    		foreach ($param as $key => $val) {
    			$aPOST[] = $key . "=" . urlencode($val);
    		}
    		$strPOST = join("&", $aPOST);
    	}
    	curl_setopt($oCurl, CURLOPT_URL, $url);
    	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($oCurl, CURLOPT_POST, true);
    	curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    	if($wxchat){
    		curl_setopt($oCurl,CURLOPT_SSLCERT,dirname(THINK_PATH).$wxchat['api_cert']);
    		curl_setopt($oCurl,CURLOPT_SSLKEY,dirname(THINK_PATH).$wxchat['api_key']);
    		curl_setopt($oCurl,CURLOPT_CAINFO,dirname(THINK_PATH).$wxchat['api_ca']);
    	}
    	$sContent = curl_exec($oCurl);
    	$aStatus = curl_getinfo($oCurl);
    	curl_close($oCurl);
    	if (intval($aStatus["http_code"]) == 200) {
    		return $sContent;
    	} else {
    		return false;
    	}
    }
    
    //支付金额原路退还
    public function payment_refund($data){
    	if(!empty($data["transaction_id"])){
    		$input = new WxPayRefund();
    		$input->SetTransaction_id($data["transaction_id"]);
    		$input->SetTotal_fee($data["total_fee"]);
    		$input->SetRefund_fee($data["refund_fee"]);
    		$input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
    		$input->SetOp_user_id(WxPayConfig::MCHID);
    		return WxPayApi::refund($input);
    	}else{
    		return false;
    	}
    }

}