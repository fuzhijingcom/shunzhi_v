<?php
define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}


class wechatCallbackapiTest
{
    //验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echoStr;
            exit;
        }
    }
    //响应消息
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("用户操作时间：".date('Y-m-d H:i:s')."\r接收内容：\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
             
            //消息类型分离
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            $this->logger("回复内容：\n".$result."\n--------------------------------------");
            echo $result;
        }else {
            echo "";
            exit;
        }
    }
    //接收事件消息
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                $content = "感谢您关注顺职实V
实V是校园综合服务平台
 
有校园活动通知、校园趣事
免VIP在线观影、二手市场
数码维修业务、综合服务功能
 
独立系统众包模式满足您的需求
PS：
1. 如遇问题请及时咨询或投诉
2. 二手交易最好面交，外链平台需收手续费
3. 校内其他需求可直接提问，客服时时解答（例如：相片打印）
4. 首条推文前三名留言者有惊喜
5. 其他功能等待您反馈建议开发
";
                $content .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";
                break;
            case "unsubscribe":
                $content = "取消关注";
                break;
            case "SCAN":
                $content = "扫描场景 ".$object->EventKey;
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "COMPANY":
                        $content = array();
                        $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                        break;
                   
                            
                    default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case "VIEW":
                $content = "跳转链接 ".$object->EventKey;
                break;
            case "MASSSENDJOBFINISH":
                $content = "消息ID：".$object->MsgID."，结果：".$object->Status."，粉丝数：".$object->TotalCount."，过滤：".$object->FilterCount."，发送成功：".$object->SentCount."，发送失败：".$object->ErrorCount;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        //多客服人工回复模式
        if (strstr($keyword, "客服")){
            $content = "进入客服？但是我是机器人啊";
        }
        else {
            //自动回复模式
          
           if (strstr(strtolower($keyword), "下单") || strstr($keyword, "代拿"))
            {
                $content ="<a href='http://v.yykddn.com/kuaidi'>点这里【我要代拿】</a>";
           }

            else if (strstr(strtolower($keyword), "提现") || strstr($keyword, "退款"))
            {
                $content = "退款提现流程：
            
1.查询你的钱包：
           
<a href='http://v.yykddn.com/Mobile/User/account.html'>我的钱包</a>
           
2.进行提现操作：
           
<a href='http://v.yykddn.com/Mobile/User/withdrawals.html'>申请提现</a>
           
（仅支持提现到支付宝）24小时处理完毕。";
            }
           
            else if (strstr(strtolower($keyword), "图") || strstr($keyword, "图片"))
            {
                $content = array();
                $content[] = array("Title"=>"全站工程师首页",  "Description"=>"全端工程师首页---这是主人设计的网站", "PicUrl"=>"http://121.42.196.145/weixin/xxn.jpg", "Url" =>"http://www.yuyuhu.com/show/m/index.php");
            }else if (strstr(strtolower($keyword), "平凡之路"))
            {
                $content = array();
                $content = array("Title"=>"平凡之路", "Description"=>"歌手：朴树", "MusicUrl"=>"http://mp3hot.9ku.com/hot/2014/07-16/642431.mp3", "HQMusicUrl"=>"http://mp3hot.9ku.com/hot/2014/07-16/642431.mp3");
            }else if (strstr(strtolower($keyword), "杀阡陌"))
            {
                $content = array();
                $content = array("Title"=>"杀阡陌", "Description"=>"歌手：马健涛", "MusicUrl"=>"http://mp3tuijian.9ku.com/tuijian/2015/07-06/665486.mp3", "HQMusicUrl"=>"http://mp3tuijian.9ku.com/tuijian/2015/07-06/665486.mp3");
            }else if (strstr(strtolower($keyword), "小苹果"))
            {
                $content = array();
                $content = array("Title"=>"小苹果", "Description"=>"歌手：筷子兄弟", "MusicUrl"=>"http://mp3hot.9ku.com/hot/2014/05-29/637791.mp3", "HQMusicUrl"=>"http://mp3hot.9ku.com/hot/2014/05-29/637791.mp3");
            }else if(strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "在吗") || strstr($keyword, "谁"))
            {
                $content = "您好，我是小晶晶/:heart 当然您可以叫我 仙女姐姐 /::>,有什么可以为您效劳的.
";
            }
            else if(strstr($keyword, "音乐") || strstr($keyword, "来一首") || strstr($keyword, "music"))
            {
                $content = "请回复歌曲名称：【平凡之路】、【杀阡陌】、【小苹果】";
            }else if(strstr($keyword, "网络") || strstr($keyword, "网络152") || strstr($keyword, "152"))
            {
                $content = "网络152的口号是：
加油
加油
加油";
            }
else if(strstr($keyword, "客服") || strstr($keyword, "咨询"))
            {
                $content = "感谢您支持实V
在您遇到权益问题时
请您不要隐忍
请在第一时间通知我们
实V团队一定在3小时内为您维权
客服微信：XXD562
客服电话：17329860373";
            }else if(strstr($keyword, "/:"))
            {
                $content = "
您发送的是表情吗？我不理解人类的表情...您可以输入 【帮助】 来获取与我沟通的技巧.
";
            }else if(strstr($keyword, "笨") || strstr($keyword, "傻") || strstr($keyword, "呆") )
            {
                $content = "我是小仙女/:heart.人类才是笨蛋,傻瓜,呆子";
            }else if(strstr(strtolower($keyword), "sb") || strstr($keyword, "滚") || strstr($keyword, "妈的") )
            {
                $content = "好孩子都是不说脏话的,您应该向我学习.";
            }else if(strstr($keyword, "代取") )
            {
            	$content['MediaId'] = "eEIeNun4w974y0iL14Wv2Sr5CgPSCO1X9GTAFaOVCwdoMTB8hTl1wDE1w3J75c7M";
            }
            
          else{
                 $content = '123';         
          }

            if(is_array($content)){
                
            	if (isset($content[0]['PicUrl'])){
                    $result = $this->transmitNews($object, $content);
                }else if (isset($content['MusicUrl'])){
                    $result = $this->transmitMusic($object, $content);
                }
                elseif($content['MediaId'] !== NULL){
                	$result = $this->transmitImage($object, $content);
                }
                
            }elseif($content !== '123'){
            	$result = $this->transmitText($object, $content);
            }
        }
        return $result;
    }


    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }
    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "哦！原来您在这！您发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array( "MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }
    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }
    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";
        $item_str = sprintf($itemTpl, $imageArray['MediaId']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[image]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";
        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[voice]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";
        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[video]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>%s</ArticleCount>
        <Articles>
        $item_str</Articles>
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";
        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        $item_str
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //日志记录
    private function logger($log_content)
    {
        
        
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 10000000000000000000000000000000000000000000000000000000000;
            $log_filename = "log.txt";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, $log_content."\r\n", FILE_APPEND);
        }
        
        
        
        
        
    }
    
    
    public function get_cishu_by_openid($openid){

        $User = M("wx");
        
        $cishu = $User->where("openid='$openid'")->getField('cishu');
        $uid = M("common_member_wechat")->where("openid='$openid'")->getField('uid');
        if($cishu == null){
            
          
            
            $sql =  "INSERT INTO `yudw`.`pre_wx` ( `uid`,`openid`, `cishu`) VALUES ( '".$uid."','".$openid."', '1');";
            $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
            $Model->execute($sql);
            
            
        }else{
            $date = date("Y-m-d");
            
           // $datesql = ;
           // $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
           // $Model->execute("UPDATE `yudw`.`pre_wx` SET `lastdate` = \'".$date."\' WHERE `pre_wx`.`uid` = '1';");
           
            $data['cishu'] = 888888;
            $data['openid'] = 888888;
            $data['uid'] = 33;
            
            M('wx')->data($data)->add();
            
            
            
            $User->where("openid='$openid'")->setField('lastdate','2018-9-9');
            $User -> where("openid='$openid'")->setInc('cishu',1); // 次数加1;
        }
    
        $cishu = $User->where("openid='$openid'")->getField('cishu');
    

        return $cishu;
    
    }
    
    
    private function getToken(){
        $token_file = THINK_PATH.'/Conf/access_token.txt';
    
        $conf_file = THINK_PATH.'/Conf/conf.txt';
        if(!$conf = json_decode(file_get_contents($conf_file))) {
            die("can not read file");
        }
        $appid = $conf->{'appid'};
        $appsecret = $conf->{'appsecret'};
        $file = file_get_contents($token_file,true);
        $result = json_decode($file,true);
        if (time() > $result['expires']){
            $data = array();
            $data['access_token'] = $this->getNewToken($appid,$appsecret);
            $data['expires']=time()+7000;
            $jsonStr =  json_encode($data);
            $fp = fopen($token_file, "w");
            fwrite($fp, $jsonStr);
            fclose($fp);
            return $data['access_token'];
        }else{
            return $result['access_token'];
        }
    }
    
    private function getNewToken($appid,$appsecret){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $access_token_Arr =  $this->https_request($url);
        return $access_token_Arr['access_token'];
    }
    private function https_request ($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $out = curl_exec($ch);
        curl_close($ch);
        return  json_decode($out,true);
    }
    
    
}
?>		 