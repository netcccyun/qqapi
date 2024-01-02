<?php
error_reporting(E_ERROR | E_PARSE);
if(defined('IN_CRONLITE'))return;
define('IN_CRONLITE', true);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
date_default_timezone_set('Asia/Shanghai');
$date = date("Y-m-d H:i:s");

require ROOT.'config.php';

@header("Cache-Control: no-store, no-cache, must-revalidate");
@header("Pragma: no-cache");

if(!$dbconfig['user']||!$dbconfig['pwd']||!$dbconfig['dbname'])
{
header('Content-type:text/html;charset=utf-8');
echo '你还没安装！<a href="/install/">点此安装</a>';
exit();
}

include_once(SYSTEM_ROOT."autoloader.php");
Autoloader::register();

$DB = new \lib\PdoHelper($dbconfig);

if($DB->query("select * from qqapi_config where 1")==FALSE)
{
header('Content-type:text/html;charset=utf-8');
echo '你还没安装！<a href="/install/">点此安装</a>';
exit();
}

$conf = [];
$result = $DB->getAll("SELECT * FROM qqapi_config");
foreach($result as $row){
	$conf[$row['k']] = $row['v'];
}
unset($result);

define('SYS_KEY', $conf['syskey']);
$password_hash='!@#%!s!0';

include_once(SYSTEM_ROOT."functions.php");

$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = (is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$sitepath.'/';

$clientip=real_ip($conf['ip_type']?$conf['ip_type']:0);
if(isset($_COOKIE["admin_token"]))
{
	$token=authcode(daddslashes($_COOKIE['admin_token']), 'DECODE', SYS_KEY);
	if($token){
		list($user, $sid) = explode("\t", $token);
		$session=md5($conf['admin_user'].$conf['admin_pwd'].$password_hash);
		if($session===$sid) {
			$islogin=1;
		}
	}
}

//QQ登录COOKIE类型
$qqlogin_type = ['qzone'=>'QQ空间', 'qun'=>'QQ群', 'vip'=>'QQ会员', 'ti'=>'手机QQ', 'tenpay'=>'财付通', 'connect'=>'QQ互联'];

//QQ互联登录COOKIE类型
$qqlogin_type_3rd = ['video'=>'腾讯视频', 'music'=>'QQ音乐', 'wenwen'=>'腾讯问问', 'dongman'=>'腾讯动漫', 'weishi'=>'微视', 'qcloud'=>'腾讯云'];

//支持保活的COOKIE类型
$cron_qqlogin_type = ['qzone'=>'QQ空间', 'vip'=>'QQ会员', 'qcloud'=>'腾讯云'];

if (!file_exists(ROOT.'install/install.lock') && file_exists(ROOT.'install/index.php')) {
	sysmsg('<h2>检测到无 install.lock 文件</h2><ul><li><font size="4">如果您尚未安装本程序，请<a href="/install/">前往安装</a></font></li><li><font size="4">如果您已经安装本程序，请手动放置一个空的 install.lock 文件到 /install 文件夹下，<b>为了您站点安全，在您完成它之前我们不会工作。</b></font></li></ul><br/><h4>为什么必须建立 install.lock 文件？</h4>它是安装保护文件，如果检测不到它，就会认为站点还没安装，此时任何人都可以安装/重装你的网站。<br/><br/>');exit;
}
