<?php
include("../includes/common.php");
$title='COOKIE列表';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="container" style="padding-top:70px;">
    <div class="col-sm-12 col-md-11 col-lg-10 center-block" style="float: none;">
<?php
$sql = " 1";
$aid = isset($_GET['aid'])?intval($_GET['aid']):null;
if($aid){
  $account = $DB->find('account', '*', ['id'=>$aid]);
  $sql = " aid='$aid'";
  $numrows=$DB->getColumn("SELECT count(*) from qqapi_cookie WHERE{$sql}");
  echo 'QQ:'.$account['uin'].' 共有'.$numrows.'个COOKIE';
}else{
  $numrows=$DB->getColumn("SELECT count(*) from qqapi_cookie WHERE{$sql}");
  echo '系统共有'.$numrows.'个COOKIE';
}
?>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead><tr><th>ID</th><th>COOKIE类型</th><th>添加/更新时间</th><th>上次使用</th><th>上次检查</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM qqapi_cookie WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $rs->fetch())
{
echo '<tr><td><b>'.$res['id'].'</b></td><td><a href="javascript:showcontent(\''.$res['content'].'\')">'.getLoginTypeName($res['type']).'</a></td><td>'.$res['addtime'].'</td><td>'.$res['usetime'].'</td><td>'.$res['checktime'].'</td><td>'.($res['status']==1?'<font color="green">正常</font>':'<font color="red">失效</font>').'</td><td><a href="javascript:updateCookie('.$res['id'].')" class="btn btn-xs btn-success">更新</a>&nbsp;<a href="javascript:delCookie('.$res['id'].')" class="btn btn-xs btn-danger">删除</a></td></tr>';
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
echo '<li><a href="cookie.php?page='.$first.$link.'">首页</a></li>';
echo '<li><a href="cookie.php?page='.$prev.$link.'">&laquo;</a></li>';
} else {
echo '<li class="disabled"><a>首页</a></li>';
echo '<li class="disabled"><a>&laquo;</a></li>';
}
$start=$page-10>1?$page-10:1;
$end=$page+10<$pages?$page+10:$pages;
for ($i=$start;$i<$page;$i++)
echo '<li><a href="cookie.php?page='.$i.$link.'">'.$i .'</a></li>';
echo '<li class="disabled"><a>'.$page.'</a></li>';
for ($i=$page+1;$i<=$end;$i++)
echo '<li><a href="cookie.php?page='.$i.$link.'">'.$i .'</a></li>';
echo '';
if ($page<$pages)
{
echo '<li><a href="cookie.php?page='.$next.$link.'">&raquo;</a></li>';
echo '<li><a href="cookie.php?page='.$last.$link.'">尾页</a></li>';
} else {
echo '<li class="disabled"><a>&raquo;</a></li>';
echo '<li class="disabled"><a>尾页</a></li>';
}
echo'</ul>';
#分页
?>
    </div>
  </div>
<script src="//cdn.staticfile.org/layer/3.1.1/layer.min.js"></script>
<script>
function updateCookie(id){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=updateCookie',
		data : {id: id},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert('COOKIE更新成功！',{
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
function delCookie(id){
	if(confirm('你确实要删除此COOKIE吗？删除后可以重新获取')){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : 'POST',
			url : 'ajax.php?act=delCookie',
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
function showcontent(content){
	layer.alert(content, {title:'查看COOKIE', shadeClose: true})
}
</script>