<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

if(!checkRefererHost())exit('{"code":403}');

@header('Content-Type: application/json; charset=UTF-8');

switch($act){
case 'set':
	if(isset($_POST['opentype'])){
		$_POST['opentype'] = implode(',',$_POST['opentype']);
	}
	foreach($_POST as $k=>$v){
		saveSetting($k, $v);
	}
	exit('{"code":0,"msg":"succ"}');
break;
case 'iptype':
	$result = [
	['name'=>'0_X_FORWARDED_FOR', 'ip'=>real_ip(0), 'city'=>get_ip_city(real_ip(0))],
	['name'=>'1_X_REAL_IP', 'ip'=>real_ip(1), 'city'=>get_ip_city(real_ip(1))],
	['name'=>'2_REMOTE_ADDR', 'ip'=>real_ip(2), 'city'=>get_ip_city(real_ip(2))]
	];
	exit(json_encode($result));
break;

case 'getUinList':
	$login = new \lib\QQLogin();
	$result = $login->uinList();
	if($result){
		exit(json_encode(['code'=>0, 'data'=>$result]));
	}else{
		exit(json_encode(['code'=>-1, 'msg'=>'未获取到已登录的QQ列表']));
	}
break;
case 'addAccount':
	$uin = $_POST['uin'];
	if(empty($uin) || !is_numeric($uin))exit('{"code":-1,"msg":"QQ号码不能为空"}');
	if($DB->find('account', '*', ['uin'=>$uin])){
		exit('{"code":-1,"msg":"该QQ已经添加过了"}');
	}
	$login = new \lib\QQLogin();
	$list = $login->uinList();
	if(!$list)exit('{"code":-1,"msg":"未获取到已登录的QQ列表"}');
	$flag = false;
	foreach($list as $row){
		if($row['uin'] == $uin){
			$nickname = $row['nickname'];
			$flag = true;
			break;
		}
	}
	if(!$flag)exit('{"code":-1,"msg":"该QQ未在当前PC登录"}');
	if($login->checkLogin($uin)){
		$aid = $DB->insert('account', ['uin'=>$uin, 'nickname'=>$nickname, 'addtime'=>'NOW()', 'status'=>'1']);
		$DB->insert('log', ['uin'=>$row['uin'], 'type'=>'default', 'action'=>'添加账号', 'time'=>'NOW()']);
		exit(json_encode(['code'=>0, 'aid'=>$aid]));
	}else{
		exit(json_encode(['code'=>-1, 'msg'=>$login->errmsg]));
	}
break;
case 'updateAccount':
	$id = intval($_POST['id']);
	$row = $DB->find('account', '*', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"QQ不存在"}');
	$login = new \lib\QQLogin();
	if($login->checkLogin($row['uin'])){
		$DB->update('account', ['status'=>'1'], ['id'=>$id]);
		$DB->insert('log', ['uin'=>$row['uin'], 'type'=>'default', 'action'=>'更新账号', 'time'=>'NOW()']);
		exit(json_encode(['code'=>0, 'uin'=>$row['uin']]));
	}else{
		exit(json_encode(['code'=>-1, 'msg'=>$login->errmsg]));
	}
break;
case 'delAccount':
	$id = intval($_POST['id']);
	$row = $DB->find('account', '*', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"QQ不存在"}');
	$DB->delete('account', ['id'=>$id]);
	$DB->delete('cookie', ['aid'=>$id]);
	exit(json_encode(['code'=>0]));
break;

case 'updateCookie':
	$id = intval($_POST['id']);
	try{
		$cookie = \lib\Logic::updateCookie($id);
		exit(json_encode(['code'=>0, 'cookie'=>$cookie]));
	}catch(Exception $e){
		exit(json_encode(['code'=>-1, 'msg'=>$e->getMessage()]));
	}
break;
case 'delCookie':
	$id = intval($_POST['id']);
	$row = $DB->find('cookie', '*', ['id'=>$id]);
	if(!$row)exit('{"code":-1,"msg":"COOKIE不存在"}');
	$DB->delete('cookie', ['id'=>$id]);
	exit(json_encode(['code'=>0]));
break;


default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}