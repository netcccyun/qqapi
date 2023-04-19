<?php
include("../includes/common.php");
$title='后台管理首页';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$count1 = $DB->getColumn("SELECT count(*) from qqapi_account");
$count2 = $DB->getColumn("SELECT count(*) from qqapi_account WHERE status=1");
$checktime = $conf['checktime'];
if(!$checktime) $checktime = '未运行';
$mysqlversion=$DB->getColumn("select VERSION()");

?>
  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 center-block" style="float: none;">
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">后台管理首页</h3></div>
<div class="list-group">
    <div class="list-group-item"><span class="glyphicon glyphicon-stats"></span> <b>ＱＱ数量：</b>共有 <b><font color="red"><?php echo $count1?></font></b> 个QQ，其中正常的有 <b><font color="red"><?php echo $count2?></font></b> 个</div>
    <div class="list-group-item"><span class="glyphicon glyphicon-time"></span> <b>检测任务：</b>上次运行时间：<font color="blue"><?php echo $checktime?></font>&nbsp;&nbsp;<a href="./set.php?mod=cron" class="btn btn-xs btn-default">查看详情</a></div>
	<div class="list-group-item text-center"><a href="./list.php" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-list"></i> ＱＱ列表</a>&nbsp;<a href="./log.php" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-list-alt"></i> 操作日志</a>&nbsp;<a href="https://github.com/netcccyun/qqapi/blob/main/admin/apidoc.md" target="_blank" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-book"></i> 接口文档</a></div>
</div>
</div>

<div class="panel panel-info">
	<div class="panel-heading">
		<h3 class="panel-title">服务器信息</h3>
	</div>
	<ul class="list-group">
		<li class="list-group-item">
			<b>PHP 版本：</b><?php echo phpversion() ?>
		</li>
		<li class="list-group-item">
			<b>MySQL 版本：</b><?php echo $mysqlversion ?>
		</li>
		<li class="list-group-item">
			<b>WEB 软件：</b><?php echo $_SERVER['SERVER_SOFTWARE'] ?>
		</li>
		<li class="list-group-item">
			<b>操作系统：</b><?php echo php_uname() ?>
		</li>
		<li class="list-group-item">
			<b>服务器时间：</b><?php echo date("Y-m-d H:i:s") ?>
		</li>
	</ul>
</div>
    </div>
  </div>