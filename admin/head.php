<?php
@header('Content-Type: text/html; charset=UTF-8');

$admin_cdnpublic = 1;
if($admin_cdnpublic==1){
	$cdnpublic = '//lib.baomitu.com/';
}elseif($admin_cdnpublic==2){
	$cdnpublic = 'https://cdn.bootcdn.net/ajax/libs/';
}elseif($admin_cdnpublic==4){
	$cdnpublic = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/';
}else{
	$cdnpublic = '//cdn.staticfile.org/';
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo $title ?></title>
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="<?php echo $cdnpublic?>jquery/2.1.4/jquery.min.js"></script>
  <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<?php if($islogin==1){?>
  <nav class="navbar navbar-fixed-top navbar-default">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">导航按钮</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="./">QQ-API管理中心</a>
      </div><!-- /.navbar-header -->
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li class="<?php echo checkIfActive(',index')?>">
            <a href="./"><span class="glyphicon glyphicon-home"></span> 后台首页</a>
          </li>
		      <li class="<?php echo checkIfActive('list')?>">
            <a href="./list.php"><span class="glyphicon glyphicon-list"></span> ＱＱ列表</a>
          </li>
          <li class="<?php echo checkIfActive('log')?>">
            <a href="./log.php"><span class="glyphicon glyphicon-list-alt"></span> 操作日志</a>
          </li>
          <li>
            <a href="https://github.com/netcccyun/qqapi/blob/main/admin/apidoc.md" target="_blank"><span class="glyphicon glyphicon-book"></span> 接口文档</a>
          </li>
          <li class="<?php echo checkIfActive('set')?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> 系统设置<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="./set.php?mod=cron">计划任务设置</a></li>
              <li><a href="./set.php?mod=app">基础参数设置</a></li>
              <li><a href="./set.php?mod=notice">邮件提醒设置</a></li>
              <li><a href="./set.php?mod=account">管理账号设置</a></li>
            </ul>
          </li>
          <li><a href="./login.php?logout"><span class="glyphicon glyphicon-log-out"></span> 退出登录</a></li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
  </nav><!-- /.navbar -->
<?php }?>