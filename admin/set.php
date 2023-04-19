<?php
/**
 * 系统设置
**/
include("../includes/common.php");
$title='系统设置';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
<?php
$mod=isset($_GET['mod'])?$_GET['mod']:null;
?>
<?php
if($mod=='cron'){
$opentype = explode(',', $conf['opentype']);
?>
<div class="panel panel-success">
<div class="panel-heading"><h3 class="panel-title">计划任务说明</h3></div>
<div class="panel-body">
<div class="alert alert-warning"><p>定时执行计划任务可实现COOKIE检测与保活，避免频繁重复登录。</p><p>根据需要使用的接口，去选择需要检测的COOKIE类型，然后将以下命令添加到计划任务定时执行，频率：1分钟1次</p></div>
<li class="list-group-item">php <?php echo ROOT?>cron.php</li>
</div>
</div>
<div class="panel panel-info">
<div class="panel-heading"><h3 class="panel-title">计划任务设置</h3></div>
<div class="panel-body">
  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
  	<div class="form-group">
	  <label class="col-sm-4 control-label">需要检测的COOKIE类型</label>
	  <div class="col-sm-8">
	<?php foreach($cron_qqlogin_type as $key=>$value){
		echo '<label class="checkbox-inline"><input type="checkbox" name="opentype[]" value="'.$key.'" '.(in_array($key,$opentype)?'checked':null).'> '.$value.'</label>';
	}?>
	  </div>
	</div><br/>
	<div class="form-group">
	  <div class="col-sm-offset-4 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
	 </div>
	</div>
  </form>
</div>
</div>
<?php
}elseif($mod=='app'){
?>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">基础参数设置</h3></div>
<div class="panel-body">
  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
	<div class="form-group">
	  <label class="col-sm-3 control-label">API接口密钥</label>
	  <div class="col-sm-9"><div class="input-group"><input type="text" name="apikey" value="<?php echo $conf['apikey']; ?>" class="form-control" required/><span class="input-group-btn"><a href="javascript:generateKey('apikey');" title="重新生成" class="btn btn-default"><i class="fa fa-refresh"></i></a></span></div></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">获取COOKIE接口</label>
	  <div class="col-sm-9"><select class="form-control" name="cookie_open" default="<?php echo $conf['cookie_open']?>"><option value="0">关闭</option><option value="1">开启</option></select><font color="green">获取COOKIE接口专用，不影响其他接口开关</font></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">获取COOKIE接口密钥</label>
	  <div class="col-sm-9"><div class="input-group"><input type="text" name="cookie_key" value="<?php echo $conf['cookie_key']; ?>" class="form-control" required/><span class="input-group-btn"><a href="javascript:generateKey('cookie_key');" title="重新生成" class="btn btn-default"><i class="fa fa-refresh"></i></a></span></div></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">调用方IP地址白名单</label>
	  <div class="col-sm-9"><input type="text" name="white_list" value="<?php echo $conf['white_list']; ?>" class="form-control" placeholder="留空则不限制IP"/><font color="green">留空则不限制IP，多个IP用|隔开</font></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">调用方IP地址获取方式</label>
	  <div class="col-sm-9"><select class="form-control" name="ip_type" default="<?php echo $conf['ip_type']?>"><option value="0">REMOTE_ADDR</option><option value="1">X_REAL_IP</option></select></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">查询缓存时长</label>
	  <div class="col-sm-9"><div class="input-group"><input type="text" name="cache_time" value="<?php echo $conf['cache_time']; ?>" class="form-control" placeholder="0或留空为不缓存"/><span class="input-group-addon">秒</span></div><font color="green">设置查询缓存之后，可以降低请求腾讯接口的频率</font></div>
	</div><br/>
	<div class="form-group">
	  <div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
	 </div>
	</div>
  </form>
</div>
</div>
<?php
}elseif($mod=='notice'){
?>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">邮件提醒设置</h3></div>
<div class="panel-body">
  <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
	<div class="form-group">
	  <label class="col-sm-3 control-label">QQ失效后邮件提醒</label>
	  <div class="col-sm-9"><select class="form-control" name="mail_open" default="<?php echo $conf['mail_open']?>"><option value="0">关闭</option><option value="1">开启</option></select></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">发信模式</label>
	  <div class="col-sm-9"><select class="form-control" name="mail_cloud" default="<?php echo $conf['mail_cloud']?>"><option value="0">SMTP发信</option><option value="1">搜狐Sendcloud</option><option value="2">阿里云邮件推送</option></select></div>
	</div><br/>
	<div id="frame_set1" style="<?php echo $conf['mail_cloud']>1?'display:none;':null; ?>">
	<div class="form-group">
	  <label class="col-sm-3 control-label">SMTP服务器</label>
	  <div class="col-sm-9"><input type="text" name="mail_smtp" value="<?php echo $conf['mail_smtp']; ?>" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">SMTP端口</label>
	  <div class="col-sm-9"><input type="text" name="mail_port" value="<?php echo $conf['mail_port']; ?>" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">邮箱账号</label>
	  <div class="col-sm-9"><input type="text" name="mail_name" value="<?php echo $conf['mail_name']; ?>" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">邮箱密码</label>
	  <div class="col-sm-9"><input type="text" name="mail_pwd" value="<?php echo $conf['mail_pwd']; ?>" class="form-control"/></div>
	</div><br/>
	</div>
	<div id="frame_set2" style="<?php echo $conf['mail_cloud']==0?'display:none;':null; ?>">
	<div class="form-group">
	  <label class="col-sm-3 control-label">API_USER</label>
	  <div class="col-sm-9"><input type="text" name="mail_apiuser" value="<?php echo $conf['mail_apiuser']; ?>" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">API_KEY</label>
	  <div class="col-sm-9"><input type="text" name="mail_apikey" value="<?php echo $conf['mail_apikey']; ?>" class="form-control"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">发信邮箱</label>
	  <div class="col-sm-9"><input type="text" name="mail_name2" value="<?php echo $conf['mail_name2']; ?>" class="form-control"/></div>
	</div><br/>
	</div>
	<div class="form-group">
	  <label class="col-sm-3 control-label">收信邮箱</label>
	  <div class="col-sm-9"><input type="text" name="mail_recv" value="<?php echo $conf['mail_recv']; ?>" class="form-control" placeholder="不填默认为发信邮箱"/></div>
	</div><br/>
	<div class="form-group">
	  <div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/><?php if($conf['mail_name']){?>[<a href="set.php?mod=mailtest">给 <?php echo $conf['mail_recv']?$conf['mail_recv']:$conf['mail_name']?> 发一封测试邮件</a>]<?php }?>
	 </div><br/>
	</div>
  </form>
</div>
<div class="panel-footer">
<span class="glyphicon glyphicon-info-sign"></span>
使用普通模式发信时，建议使用QQ邮箱，SMTP服务器smtp.qq.com，端口465，密码不是QQ密码也不是邮箱独立密码，是QQ邮箱设置界面生成的<a href="https://service.mail.qq.com/detail/0/75"  target="_blank" rel="noreferrer">授权码</a>。<br/>阿里云邮件推送：<a href="https://www.aliyun.com/product/directmail" target="_blank" rel="noreferrer">点此进入</a>｜<a href="https://usercenter.console.aliyun.com/#/manage/ak" target="_blank" rel="noreferrer">获取AK/SK</a>
</div>
</div>
<script>
$("select[name='mail_cloud']").change(function(){
	if($(this).val() == 0){
		$("#frame_set1").show();
		$("#frame_set2").hide();
	}else{
		$("#frame_set1").hide();
		$("#frame_set2").show();
	}
});
</script>
<?php
}elseif($mod=='account_n' && $_POST['do']=='submit'){
	if(!checkRefererHost())exit;
	$user=$_POST['user'];
	$oldpwd=$_POST['oldpwd'];
	$newpwd=$_POST['newpwd'];
	$newpwd2=$_POST['newpwd2'];
	if($user==null)showmsg('用户名不能为空！',3);
	saveSetting('admin_user',$user);
	if(!empty($newpwd) && !empty($newpwd2)){
		if($oldpwd!=$conf['admin_pwd'])showmsg('旧密码不正确！',3);
		if($newpwd!=$newpwd2)showmsg('两次输入的密码不一致！',3);
		saveSetting('admin_pwd',$newpwd);
	}
	showmsg('修改成功！请重新登录',1);
}elseif($mod=='account'){
?>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">管理员账号设置</h3></div>
<div class="panel-body">
  <form action="./set.php?mod=account_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit"/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">用户名</label>
	  <div class="col-sm-9"><input type="text" name="user" value="<?php echo $conf['admin_user']; ?>" class="form-control" required/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">旧密码</label>
	  <div class="col-sm-9"><input type="password" name="oldpwd" value="" class="form-control" placeholder="请输入当前的管理员密码"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">新密码</label>
	  <div class="col-sm-9"><input type="password" name="newpwd" value="" class="form-control" placeholder="不修改请留空"/></div>
	</div><br/>
	<div class="form-group">
	  <label class="col-sm-3 control-label">重输密码</label>
	  <div class="col-sm-9"><input type="password" name="newpwd2" value="" class="form-control" placeholder="不修改请留空"/></div>
	</div><br/>
	<div class="form-group">
	  <div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
	 </div>
	</div>
  </form>
</div>
</div>
<?php
}
?>
    </div>
  </div>
<script src="//cdn.staticfile.org/layer/3.1.1/layer.min.js"></script>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
function saveSetting(obj){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=set',
		data : $(obj).serialize(),
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert('设置保存成功！', {
					icon: 1,
					closeBtn: false
				}, function(){
				  window.location.reload()
				});
			}else{
				layer.alert(data.msg, {icon: 2})
			}
		},
		error:function(data){
			layer.close(ii);
			layer.msg('服务器错误');
		}
	});
	return false;
}
function generateKey(name){
	var len = 16;
	var str = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
	var pass = '';
    for (var i = 0; i < len; i++ ) 
        pass += str.charAt(Math.floor( Math.random() * str.length));
	$("input[name="+name+"]").val(pass)
}
</script>