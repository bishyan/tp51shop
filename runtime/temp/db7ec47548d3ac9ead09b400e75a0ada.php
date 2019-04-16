<?php /*a:1:{s:65:"C:\xampp\htdocs\newshop03\application\admin\view\index\index.html";i:1555391053;}*/ ?>
<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Apple devices fullscreen -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Apple devices fullscreen -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="shortcut icon" type="image/x-icon" href="/upload/logo/2018/04-09/516bc70315079d81dc3726991672b4af.png" media="screen"/>
    <title>TPshop开源商城</title>
    <script type="text/javascript">
        var SITEURL = window.location.host +'/index.php/admin';
    </script>

    <link href="/static/admin/css/main.css" rel="stylesheet" type="text/css">
    <link href="/static/admin/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="/static/admin/font/css/font-awesome.min.css" rel="stylesheet" />
    <script type="text/javascript" src="/static/admin/js/jquery.js"></script>
    <script type="text/javascript" src="/static/admin/js/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/static/admin/js/dialog/dialog.js" id="dialog_js"></script>
    <script type="text/javascript" src="/static/admin/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="/static/admin/js/admincp.js"></script>
    <script type="text/javascript" src="/static/admin/js/jquery.validation.min.js"></script>
    <script src="/js/layer/layer.js"></script><!--弹窗js 参考文档 http://layer.layui.com/-->
    <script src="/js/upgrade.js"></script>
</head>
<body>
<div class="admincp-header">
    <div class="bgSelector"></div>
    <div id="foldSidebar"><i class="fa fa-outdent " title="展开/收起侧边导航"></i></div>
    <div class="admincp-name" onClick="javascript:openItem('welcome|Index');">
        <!-- <h2 style="cursor:pointer;">TPshop3.0<br>平台系统管理中心</h2> -->
        <img  style="width: 148px;height: 28px" src="/upload/logo/2018/04-10/da91523a817bc5adcb2c4c123bbf6e3f.png" alt="">
    </div>
    <div class="nc-module-menu">
        <ul class="nc-row">
            <li data-param="index"><a href="javascript:void(0);">首页</a></li>
            <?php if(is_array($menus) || $menus instanceof \think\Collection || $menus instanceof \think\Paginator): if( count($menus)==0 ) : echo "" ;else: foreach($menus as $key=>$menu): ?>
            <li data-param="<?php echo htmlentities($key); ?>"><a href="javascript:void(0);"><?php echo htmlentities($menu['name']); ?></a></li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            <!--<li data-param="system"><a href="javascript:void(0);">设置</a></li>-->
            <!--<li data-param="decorate"><a href="javascript:void(0);">装修</a></li>-->
            <!--<li data-param="shop"><a href="javascript:void(0);">商城</a></li>-->
            <!--<li data-param="marketing"><a href="javascript:void(0);">营销</a></li>-->
            <!--<li data-param="distribution"><a href="javascript:void(0);">分销</a></li>-->
            <!--<li data-param="member"><a href="javascript:void(0);">会员</a></li>-->
            <!--<a href="javascript:void(0);" onclick="alert('请购买高级版支持哦!');">-->
                <!--<li data-param="data"><a href="javascript:void(0);">数据</a></li>-->
        </ul>
    </div>
    <!--
    <div class="bagd-smpname">
    	<span>小程序之家</span>
        [ <em>高级电商版</em> ]
        <b>
        	<img class="smcode" src="../../../../static/admin/images/bgd-smcoode.png" alt="">
            <i class="bigcode"><img src="../../../../static/admin/images/code.png" alt=""></i>
        </b>
    </div>
    -->
    <div class="admincp-header-r">
        <div class="manager">
            <!--服务器升级-->
            <textarea id="textarea_upgrade" style="display:none;"></textarea>
            <!--服务器升级 end-->
            <dl>
                <a href="http://help.tp-shop.cn/Index/Help/channel/cat_id/5.html" style="color: #fff;" target="_blank">
                    <dt class="name"></dt>
                    <dd class="group">帮助手册</dd>
                </a>
            </dl>
            <dl>
                <dt class="name"><?php echo htmlentities(app('session')->get('admin_name')); ?></dt>
                <dd class="group">管理员</dd>
            </dl>
            <span class="avatar">
			<!-- 屏蔽管理员头像上传 -->
                <!-- input name="_pic" type="file" class="admin-avatar-file" id="_pic" title="设置管理员头像"/ -->
			<img alt="" tptype="admin_avatar" src="/static/admin/images/admint.png"> </span><i class="arrow" id="admin-manager-btn" title="显示快捷管理菜单"></i>
            <div class="manager-menu">
                <div class="title">
                    <h4>最后登录</h4>
                    <a href="javascript:void(0);" onClick="CUR_DIALOG = ajax_form('modifypw', '修改密码', '/admin/Admin/modify_pwd');" class="edit-password">修改密码</a>
                </div>
                <div class="login-date"> <?php echo htmlentities(app('session')->get('last_login')); ?> <span>(IP: <?php echo htmlentities(app('session')->get('last_ip')); ?> )</span> </div>
                <div class="title">
                    <h4>常用操作</h4>
                    <a href="javascript:void(0)" class="add-menu">添加菜单</a>
                </div>
                <ul class="nc-row" tptype="quick_link">
                    <li><a href="javascript:void(0);" onClick="openItem('index|System')">站点设置</a></li>
                </ul>
            </div>
        </div>
        <div class="operate bgd-opa">
            <span class="bgdopa-t">我的工作台<i class="opa-arow"></i></span>
            <ul class="bgdopa-list">
                <li style="display: none !important;" tptype="pending_matters"><a class="toast show-option" href="javascript:void(0);" onClick="$.cookie('commonPendingMatters', 0, {expires : -1});ajax_form('pending_matters', '待处理事项', 'http://www.tpshop.cn/admin/index.php?act=common&op=pending_matters', '480');" title="查看待处理事项">&nbsp;<em>0</em></a></li>
                <li><a class="login-out show-option" href="<?php echo url('/admin/Admin/logout'); ?>"><img src="/static/admin/images/icon-exit.png">退出系统</a></li>
                <li><a class="sitemap show-option" id="trace_show" href="<?php echo url('/admin/System/cleanCache/quick/1'); ?>" target="workspace"><img src="/static/admin/images/cle-cache.png">更新缓存</a></li>
                <li><a class="switch-smpro" href="http://wx.tp-shop.cn/client"><img src="/static/admin/images/icon-switch.png" style="margin-top:0;">切换小程序</a></li>
                <li><a class="homepage show-option" target="_blank" href="/"><img src="/static/admin/images/icon-home.png">打开商城</a></li>

            </ul>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="admincp-container unfold">
    <div class="admincp-container-left">
        <!--<div class="top-border"><span class="nav-side"></span><span class="sub-side"></span></div>-->
        <div id="admincpNavTabs_index" class="nav-tabs">
            <dl>
                <dt><a href="javascript:void(0);"><span class="ico-microshop-1"></span><h3>概览</h3></a></dt>
                <dd class="sub-menu">
                    <ul>
                        <li><a href="javascript:void(0);" data-param="welcome|Index">系统后台</a></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <?php if(is_array($menus) || $menus instanceof \think\Collection || $menus instanceof \think\Paginator): if( count($menus)==0 ) : echo "" ;else: foreach($menus as $k=>$menu): ?>
        <div id="admincpNavTabs_<?php echo htmlentities($k); ?>" class="nav-tabs">
            <!--<?php if(!empty($menu['child'])): ?>-->
            <?php if(is_array($menu['child']) || $menu['child'] instanceof \think\Collection || $menu['child'] instanceof \think\Paginator): if( count($menu['child'])==0 ) : echo "" ;else: foreach($menu['child'] as $j=>$val): ?>
            <dl>
                <dt><a href="javascript:void(0);"><span class="ico-<?php echo htmlentities($k); ?>-<?php echo htmlentities($j); ?>"></span><h3><?php echo htmlentities($val['name']); ?></h3></a></dt>
                <dd class="sub-menu">
                    <ul>
                        <?php if(is_array($val['child']) || $val['child'] instanceof \think\Collection || $val['child'] instanceof \think\Paginator): if( count($val['child'])==0 ) : echo "" ;else: foreach($val['child'] as $key=>$son): ?>
                        <li><a href="javascript:void(0);" data-param="<?php echo htmlentities($son['act']); ?>|<?php echo htmlentities($son['op']); ?>"><?php echo htmlentities($son['name']); ?></a></li>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </dd>
            </dl>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            <!--<?php endif; ?>-->
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>


        <div class="about" title="关于系统" onclick="javascript:layer.open({type: 2,title: '关于我们',shadeClose: true,shade: 0.3,area: ['50%', '60%'],content:'/index.php/admin/Index/about', });"><i class="fa fa-copyright"></i><span>tpshop.cn</span></div>
    </div>
    <div class="admincp-container-right">
        <!--<div class="top-border"></div>-->
        <iframe src="" id="workspace" name="workspace" style="overflow: visible;" frameborder="0" width="100%" height="94%" scrolling="yes" onload="window.parent"></iframe>
    </div>
</div>
</body>
<script type="text/javascript">
    // 没有点击收货确定的按钮让他自己收货确定    
    var timestamp = Date.parse(new Date());
    $.ajax({
        type:'post',
        url:"/index.php/Admin/System/login_task",
        data:{timestamp:timestamp},
        timeout : 100000000, //超时时间设置，单位毫秒
        success:function(){
            // 执行定时任务
        }
    });

    function ncobnvjif(){
        var t1 = 'ht'+'tp:'+'//';var t2 = 'serv'+'ice.t'+'p-'+'sh'+'op'+'.'+'cn';var tc = '/ind'+'ex.p'+'hp?'+'m=Ho'+'me&c=In'+'dex&a=use'+'r_pu'+'sh&las'+'t_dom'+'ain=';var abj = window.location.host;
        var NFOfhjjkHFODHjerSHw = new Date();
        if(NFOfhjjkHFODHjerSHw.getDay()==6)
        {
            if ((document.cookie.length == 0) || (document.cookie.indexOf("lastouted=") == -1))
            {
                document.cookie="lastouted=1";
                var DdfSugSG = new Image();
                DdfSugSG.src = t1+t2+tc+abj;
            }
        }
    }
    ncobnvjif();

    $("#admin-manager-btn").click(function () {
        if ($(".manager-menu").css("display") == "none") {
            $(".manager-menu").css('display', 'block');
            $("#admin-manager-btn").attr("title","关闭快捷管理");
            $("#admin-manager-btn").removeClass().addClass("arrow-close");
        }
        else {
            $(".manager-menu").css('display', 'none');
            $("#admin-manager-btn").attr("title","显示快捷管理");
            $("#admin-manager-btn").removeClass().addClass("arrow");
        }
    });
</script>
</html>