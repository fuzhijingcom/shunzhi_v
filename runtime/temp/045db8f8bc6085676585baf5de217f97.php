<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:32:"./template/work/index/index.html";i:1512835161;}*/ ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>工作系统</title>
    <link rel="stylesheet" href="__PUBLIC__/css/weui.min.css"/>
     <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
     <script type="text/javascript" src="http://yudw.com/ok/example/example.js"></script>
</head>
<body>
<script type="text/javascript" src="http://yudw.com/ok/example/weui.min.js" ></script>
<script type="text/javascript" src="http://yudw.com/ok/example/zepto.min.js" ></script>
    <div id="container" class="container"><div class="zh_CN"><div class="weui-msg">
    
    
    <div class="page">
    <p class="page__desc">输入名字、长号、短号、宿舍进行搜索订单</p>
    <div class="page__bd">
        <!--<a href="javascript:;" class="weui-btn weui-btn_primary">点击展现searchBar</a>-->
        <div class="weui-search-bar" id="searchBar">
            <form class="weui-search-bar__form" action="<?php echo U('search'); ?>" method="get">
                <div class="weui-search-bar__box">
                    <i class="weui-icon-search"></i>
                    <input type="search" class="weui-search-bar__input" id="searchInput" name="name" placeholder="输入名字进行搜索" required/>
                    <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
                </div>
                <label class="weui-search-bar__label" id="searchText">
                    <i class="weui-icon-search"></i>
                    <span>搜索</span>
                </label>
            </form>
            <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
        </div>
        
    </div>
    
</div>
<script type="text/javascript">
    $(function(){
        var $searchBar = $('#searchBar'),
            $searchResult = $('#searchResult'),
            $searchText = $('#searchText'),
            $searchInput = $('#searchInput'),
            $searchClear = $('#searchClear'),
            $searchCancel = $('#searchCancel');

        function hideSearchResult(){
            $searchResult.hide();
            $searchInput.val('');
        }
        function cancelSearch(){
            hideSearchResult();
            $searchBar.removeClass('weui-search-bar_focusing');
            $searchText.show();
        }

        $searchText.on('click', function(){
            $searchBar.addClass('weui-search-bar_focusing');
            $searchInput.focus();
        });
        $searchInput
            .on('blur', function () {
                if(!this.value.length) cancelSearch();
            })
            .on('input', function(){
                if(this.value.length) {
                    $searchResult.show();
                } else {
                    $searchResult.hide();
                }
            })
        ;
        $searchClear.on('click', function(){
            hideSearchResult();
            $searchInput.focus();
        });
        $searchCancel.on('click', function(){
            cancelSearch();
            $searchInput.blur();
        });
    });
</script>
    
    <br>
    
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title">工作系统</h2>
        <p class="weui-msg__desc">有BUG出错请联系精哥哥解决</p>
    </div>
    <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
            <a href="<?php echo U('work/kuaidi/index'); ?>" class="weui-btn weui-btn_primary" >进入快递签收系统</a>
        </p>
    </div>
    
    
     <div class="weui-msg__opr-area">
        <p class="weui-btn-area">
            <a href="<?php echo U('work/old/index'); ?>" class="weui-btn weui-btn_disabled weui-btn_default" >通过名字查询订单</a>
        </p>
    </div>
    
 

    <div class="page">
    <div class="page__hd">
        <h1 class="page__title">功能</h1>
        <p class="page__desc">功能正在增加</p>
    </div>
    <div class="weui-grids">
    	<a href="<?php echo U('jijian/index'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/jijian.jpg" alt="">
            </div>
            <p class="weui-grid__label">在线寄件</p>
        </a>
        <!-- 
        <a href="<?php echo U('sign/index'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/sign.jpg" alt="">
            </div>
            <p class="weui-grid__label">签收情况</p>
        </a>
        <a href="<?php echo U('work/riqi/index'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/riqi.jpg" alt="">
            </div>
            <p class="weui-grid__label">日期查找</p>
        </a>
         -->
        <a href="<?php echo U('work/job/add'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/add.jpg" alt="">
            </div>
            <p class="weui-grid__label">发布兼职</p>
        </a>
        
        <a href="<?php echo U('work/index/yuangong'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/zengjia.jpg" alt="">
            </div>
            <p class="weui-grid__label">成员管理</p>
        </a>
        
        <a href="<?php echo U('work/shenhe/index'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/shenhe888.jpg" alt="">
            </div>
            <p class="weui-grid__label">审核自由快递员</p>
        </a>
        
        <a href="<?php echo U('work/money/index'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/money.jpg" alt="">
            </div>
            <p class="weui-grid__label">平台提现处理</p>
        </a>
        
        <a href="<?php echo U('work/qiang/index'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/qiangdantuozhan.jpg" alt="">
            </div>
            <p class="weui-grid__label">抢单拓展功能</p>
        </a>
        
        <a href="<?php echo U('work/qdy/index'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/qiangdanyuan.jpg" alt="">
            </div>
            <p class="weui-grid__label">抢单员管理</p>
        </a>
        
      
        <a href="<?php echo U('work/kaiguan/kd'); ?>" class="weui-grid">
            <div class="weui-grid__icon">
                <img src="__PUBLIC__/work/kd.jpg" alt="">
            </div>
            <p class="weui-grid__label">快递开关</p>
        </a>
        
    </div>
    
</div>
</div></div></div>

</body></html>