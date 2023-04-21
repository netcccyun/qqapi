<?php
include("../includes/common.php");
$title='ＱＱ列表';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<style>
.table>tbody>tr>td {
	vertical-align: middle;
    max-width: 360px;
	word-break: break-all;
}
.img-circle{margin-right: 7px;}
</style>
  <div class="container" style="padding-top:70px;">
    <div class="col-sm-12 col-md-11 col-lg-10 center-block" style="float: none;">
<?php

if($_GET['my']=='search') {
	$sql=" `uin`='{$_GET['kw']}'";
	$numrows=$DB->getColumn("SELECT count(*) from qqapi_account WHERE{$sql}");
	$con='包含 '.$_GET['kw'].' 的共有 <b>'.$numrows.'</b> 个记录';
	$link='&my=search&kw='.$_GET['kw'];
}else{
	$numrows=$DB->getColumn("SELECT count(*) from qqapi_account WHERE 1");
	$sql=" 1";
	$con='本站共有 <b>'.$numrows.'</b> 个QQ';
}

echo '<form action="list.php" method="GET" class="form-inline"><input type="hidden" name="my" value="search">
<div class="form-group">
  <label>搜索</label>
  <input type="text" class="form-control" name="kw" placeholder="QQ号码">
</div>
<button type="submit" class="btn btn-primary">搜索</button>&nbsp;<a href="javascript:addqq()" class="btn btn-success">添加ＱＱ</a>
</form>';
echo $con;
?>
<div class="modal" id="modal-store" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content animated flipInX">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span
							aria-hidden="true">&times;</span><span
							class="sr-only">Close</span></button>
				<h4 class="modal-title" id="modal-title">添加QQ</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" id="form-store">
					<div class="form-group">
						<label class="col-sm-2 control-label">选择QQ</label>
						<div class="col-sm-10">
							<select name="type" id="uin" class="form-control">
							</select>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
				<button type="button" class="btn btn-primary" id="store" onclick="addAccount()">确认添加</button>
			</div>
		</div>
	</div>
</div>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead><tr><th>ID</th><th>头像</th><th>QQ号码</th><th>昵称</th><th>添加时间</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM qqapi_account WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $rs->fetch())
{
  $avatar = 'http://q2.qlogo.cn/headimg_dl?dst_uin='.$res['uin'].'&spec=5';
echo '<tr><td><b>'.$res['id'].'</b></td><td><img src="'.$avatar.'" alt="Avatar" width="40" class="img-circle"></td><td>'.$res['uin'].'</td><td>'.$res['nickname'].'</td><td>'.$res['addtime'].'</td><td>'.($res['status']==1?'<font color="green">正常</font>':'<font color="red">离线</font>').'</td><td>'.($res['status']==0?'<a href="javascript:updateAccount('.$res['id'].')" class="btn btn-xs btn-warning">更新</a>&nbsp;':'').'<a href="./cookie.php?aid='.$res['id'].'" class="btn btn-xs btn-info">COOKIE</a>&nbsp;<a href="./log.php?my=search&kw='.$res['uin'].'" class="btn btn-xs btn-default">日志</a>&nbsp;<a href="javascript:delAccount('.$res['id'].')" class="btn btn-xs btn-danger">删除</a></td></tr>';
}
?>
          </tbody>
        </table>
      </div>
<?php
echo'<ul class="pagination">';
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1)
{
echo '<li><a href="list.php?page='.$first.$link.'">首页</a></li>';
echo '<li><a href="list.php?page='.$prev.$link.'">&laquo;</a></li>';
} else {
echo '<li class="disabled"><a>首页</a></li>';
echo '<li class="disabled"><a>&laquo;</a></li>';
}
$start=$page-10>1?$page-10:1;
$end=$page+10<$pages?$page+10:$pages;
for ($i=$start;$i<$page;$i++)
echo '<li><a href="list.php?page='.$i.$link.'">'.$i .'</a></li>';
echo '<li class="disabled"><a>'.$page.'</a></li>';
for ($i=$page+1;$i<=$end;$i++)
echo '<li><a href="list.php?page='.$i.$link.'">'.$i .'</a></li>';
echo '';
if ($page<$pages)
{
echo '<li><a href="list.php?page='.$next.$link.'">&raquo;</a></li>';
echo '<li><a href="list.php?page='.$last.$link.'">尾页</a></li>';
} else {
echo '<li class="disabled"><a>&raquo;</a></li>';
echo '<li class="disabled"><a>尾页</a></li>';
}
echo'</ul>';
?>
    </div>
  </div>
<script src="//cdn.staticfile.org/layer/3.1.1/layer.min.js"></script>
<script>
function addqq(){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'ajax.php?act=getUinList',
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				$("#modal-store").modal('show');
				$("#uin").empty();
				$.each(data.data, function (i, res) {
					$("#uin").append('<option value="'+res+'">'+res+'</option>');
				})
			}else{
				layer.alert(data.msg, {icon: 2})
			}
		}
	});
}
function addAccount(){
	var uin = $("#uin").val();
	if(uin == ''){
		layer.alert('请选择QQ');return;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=addAccount',
		data : {uin: uin},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert('QQ:'+uin+'添加成功！',{
					icon: 1,
					closeBtn: false
				}, function(){
				  window.location.reload()
				});
			}else{
				layer.alert(data.msg, {icon: 2})
			}
		}
	});
}
function updateAccount(id){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=updateAccount',
		data : {id: id},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert('QQ:'+data.uin+'更新成功！',{
					icon: 1,
					closeBtn: false
				}, function(){
				  window.location.reload()
				});
			}else{
				layer.alert(data.msg, {icon: 2})
			}
		}
	});
}
function delAccount(id){
	if(confirm('你确实要删除此QQ吗？')){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : 'POST',
			url : 'ajax.php?act=delAccount',
			data : {id: id},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					alert('删除成功！');
					window.location.reload()
				}else{
					layer.alert(data.msg, {icon: 2})
				}
			}
		});
	}
}
</script>