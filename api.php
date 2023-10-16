<?php
include("./includes/common.php");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

@header('Content-Type: application/json; charset=UTF-8');

$opentype = explode(',', $conf['opentype']);
$key = isset($_POST['key'])?trim($_POST['key']):trim($_GET['key']);
if(!$key) exit('{"code":-1,"msg":"No key"}');

switch($act){
case 'getcookie': //获取指定COOKIE
	$type = isset($_POST['type'])?trim($_POST['type']):exit('{"code":-1,"msg":"No type"}');
	$uin = isset($_POST['uin'])?trim($_POST['uin']):null;
	if($conf['cookie_open']!=1)exit('{"code":-1,"msg":"未开启获取COOKIE接口"}');
	if($key !== $conf['cookie_key'])exit('{"code":-1,"msg":"密钥错误"}');
	if(!empty($uin)){
		$account = $DB->find('account', '*', ['uin'=>$uin]);
		if(!$account) exit('{"code":-1,"msg":"QQ不存在"}');
		if($account['status']!=1) exit('{"code":-1,"msg":"QQ状态不正常"}');
		$row = $DB->getRow("SELECT * FROM qqapi_cookie WHERE aid=:aid AND `type`=:type LIMIT 1", [':aid'=>$account['id'], ':type'=>$type]);
		if($row){
			if($row['status'] == 1 && (in_array($type, $opentype) || strtotime($row['addtime']) + 600 > time())){
				$cookie = $row['content'];
			}else{
				try{
					$cookie = \lib\Logic::updateCookie($row['id']);
				}catch(Exception $e){
					exit('{"code":-1,"msg":"'.$e->getMessage().'"}');
				}
			}
			$DB->update('cookie', ['usetime'=>'NOW()'], ['id'=>$row['id']]);
		}else{
			try{
				$cookie = \lib\Logic::addCookie($account['id'], $type);
			}catch(Exception $e){
				exit('{"code":-1,"msg":"'.$e->getMessage().'"}');
			}
		}
	}else{
		$row = $DB->getRow("SELECT A.uin,A.id aid,B.id,B.content,B.status,B.addtime FROM qqapi_account A LEFT JOIN qqapi_cookie B ON A.id=B.aid AND B.`type`=:type WHERE A.status=1 ORDER BY usetime ASC LIMIT 1", [':type'=>$type]);
		if(!$row) exit('{"code":-1,"msg":"暂无可用的QQ"}');
		$uin = $row['uin'];
		if($row['id']){
			if($row['status'] == 1 && (in_array($type, $opentype) || strtotime($row['addtime']) + 600 > time())){
				$cookie = $row['content'];
			}else{
				try{
					$cookie = \lib\Logic::updateCookie($row['id']);
				}catch(Exception $e){
					exit('{"code":-1,"msg":"'.$e->getMessage().'"}');
				}
			}
			$DB->update('cookie', ['usetime'=>'NOW()'], ['id'=>$row['id']]);
		}else{
			try{
				$cookie = \lib\Logic::addCookie($row['aid'], $type);
			}catch(Exception $e){
				exit('{"code":-1,"msg":"'.$e->getMessage().'"}');
			}
		}
	}
	exit(json_encode(['code'=>0, 'uin'=>$uin, 'type'=>$type, 'cookie'=>$cookie]));
break;
case 'getclientkey': //获取clientkey
	$uin = isset($_POST['uin'])?trim($_POST['uin']):null;
	if($conf['cookie_open']!=1)exit('{"code":-1,"msg":"未开启获取COOKIE接口"}');
	if($key !== $conf['cookie_key'])exit('{"code":-1,"msg":"密钥错误"}');
	if(!empty($uin)){
		$account = $DB->find('account', '*', ['uin'=>$uin]);
		if(!$account) exit('{"code":-1,"msg":"QQ不存在"}');
		if($account['status']!=1) exit('{"code":-1,"msg":"QQ状态不正常"}');
	}else{
		$row = $DB->getRow("SELECT * FROM qqapi_account WHERE status=1 ORDER BY rand() LIMIT 1");
		if(!$row) exit('{"code":-1,"msg":"暂无可用的QQ"}');
		$uin = $row['uin'];
	}
	$qqlogin = new \lib\QQLogin();
	$clientkey = $qqlogin->getClientKey($uin);
	if($clientkey!==false){
		exit(json_encode(['code'=>0, 'uin'=>$uin, 'clientkey'=>$clientkey]));
	}else{
		exit(json_encode(['code'=>-1, 'msg'=>'clientkey获取失败']));
	}
break;
case 'getoauthcode': //获取QQ互联登录授权CODE
	$uin = isset($_POST['uin'])?trim($_POST['uin']):null;
	$client_id = isset($_POST['client_id'])?trim($_POST['client_id']):exit('{"code":-1,"msg":"No client_id"}');
	$redirect_uri = isset($_POST['redirect_uri'])?trim($_POST['redirect_uri']):exit('{"code":-1,"msg":"No redirect_uri"}');
	if($conf['cookie_open']!=1)exit('{"code":-1,"msg":"未开启获取COOKIE接口"}');
	if($key !== $conf['cookie_key'])exit('{"code":-1,"msg":"密钥错误"}');
	if(!empty($uin)){
		$account = $DB->find('account', '*', ['uin'=>$uin]);
		if(!$account) exit('{"code":-1,"msg":"QQ不存在"}');
		if($account['status']!=1) exit('{"code":-1,"msg":"QQ状态不正常"}');
	}else{
		$row = $DB->getRow("SELECT * FROM qqapi_account WHERE status=1 ORDER BY rand() LIMIT 1");
		if(!$row) exit('{"code":-1,"msg":"暂无可用的QQ"}');
		$uin = $row['uin'];
	}
	$qqlogin = new \lib\QQLogin();
	$oauthcode = $qqlogin->login3rdapi($uin, $client_id, $redirect_uri);
	if($oauthcode!==false){
		exit(json_encode(['code'=>0, 'uin'=>$uin, 'oauthcode'=>$oauthcode]));
	}else{
		exit(json_encode(['code'=>-1, 'msg'=>$qqlogin->errmsg]));
	}
break;
case 'getshuoshuo': //获取说说列表
	$uin = isset($_POST['uin'])?trim($_POST['uin']):exit('{"code":-1,"msg":"No key"}');
	$page = isset($_POST['page'])?$_POST['page']:1;
	if($key !== $conf['apikey'])exit('{"code":-1,"msg":"密钥错误"}');
	$result = qqtool_call('qzone', 'getshuoshuo', [$uin, $page]);
	exit(json_encode($result));
break;
case 'getrizhi': //获取日志列表
	$uin = isset($_POST['uin'])?trim($_POST['uin']):exit('{"code":-1,"msg":"No key"}');
	$page = isset($_POST['page'])?$_POST['page']:1;
	if($key !== $conf['apikey'])exit('{"code":-1,"msg":"密钥错误"}');
	$result = qqtool_call('qzone', 'getrizhi', [$uin, $page]);
	exit(json_encode($result));
break;
case 'getvisitcount': //获取空间人气
	$uin = isset($_POST['uin'])?trim($_POST['uin']):exit('{"code":-1,"msg":"No key"}');
	if($key !== $conf['apikey'])exit('{"code":-1,"msg":"密钥错误"}');
	$result = qqtool_call('qzone', 'getvisitcount', [$uin]);
	exit(json_encode($result));
break;
case 'getprivilege': //获取已开通权益
	$uin = isset($_POST['uin'])?trim($_POST['uin']):exit('{"code":-1,"msg":"No key"}');
	if($key !== $conf['apikey'])exit('{"code":-1,"msg":"密钥错误"}');
	$result = qqtool_call('vip', 'getprivilege', [$uin]);
	exit(json_encode($result));
break;
case 'getqqlevel': //获取QQ等级
	$uin = isset($_POST['uin'])?trim($_POST['uin']):exit('{"code":-1,"msg":"No key"}');
	if($key !== $conf['apikey'])exit('{"code":-1,"msg":"密钥错误"}');
	$result = qqtool_call('vip', 'getqqlevel', [$uin]);
	exit(json_encode($result));
break;
case 'getqqnick': //获取QQ昵称
	$uin = isset($_POST['uin'])?trim($_POST['uin']):exit('{"code":-1,"msg":"No key"}');
	if($key !== $conf['apikey'])exit('{"code":-1,"msg":"密钥错误"}');
	$result = qqtool_call('qzone', 'getqqnick', [$uin]);
	exit(json_encode($result));
break;
default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}