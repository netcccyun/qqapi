<?php
if (substr(php_sapi_name(), 0, 3) != 'cli') {
    die("This Programe can only be run in CLI mode");
}
@chdir(dirname(__FILE__));
include("./includes/common.php");

$opentype = explode(',', $conf['opentype']);

foreach($opentype as $type){
    if(empty($type)) continue;
    $checktime = date("Y-m-d H:i:s",strtotime("-30 seconds"));
    $list = $DB->getAll("SELECT A.uin,A.id aid,B.id,B.content,B.status,B.addtime FROM qqapi_account A LEFT JOIN qqapi_cookie B ON A.id=B.aid AND B.`type`=:type WHERE A.status=1 AND (B.checktime<'$checktime' OR B.checktime IS NULL) ORDER BY usetime ASC", [':type'=>$type]);
    if(count($list) == 0) exit("[OK] 暂无需要更新的COOKIE\n");
    foreach($list as $row){
        $uin = $row['uin'];
        if($row['id']){
            if($row['status'] == 1){
                if(!\lib\QQCheck::checkCookie($type, $row['uin'], $row['content'])){
                    $DB->update('cookie', ['status'=>'0'], ['id'=>$row['id']]);
                    try{
                        \lib\Logic::updateCookie($row['id']);
                        echo "[OK] {$uin}|{$type} 更新COOKIE成功\n";
                    }catch(Exception $e){
                        echo "[Error] {$uin}|{$type} 更新COOKIE失败,{$e->getMessage()}\n";
                    }
                }else{
                    echo "[OK] {$uin}|{$type} 已检测COOKIE正常\n";
                }
            }else{
                try{
                    \lib\Logic::updateCookie($row['id']);
                    echo "[OK] {$uin}|{$type} 更新COOKIE成功\n";
                }catch(Exception $e){
                    echo "[Error] {$uin}|{$type} 更新COOKIE失败,{$e->getMessage()}\n";
                }
            }
            $DB->update('cookie', ['checktime'=>'NOW()'], ['id'=>$row['id']]);
        }else{
            try{
                \lib\Logic::addCookie($row['aid'], $type);
                echo "[OK] {$uin}|{$type} 更新COOKIE成功\n";
            }catch(Exception $e){
                echo "[Error] {$uin}|{$type} 添加COOKIE失败,{$e->getMessage()}\n";
            }
        }
        sleep(1);
    }
}

if($conf['cache_time'] > 0 && $conf['cache_clean']!=date('Ymd')){
    $DB->exec("TRUNCATE TABLE `qqapi_cache`");
    saveSetting('cache_clean', date('Ymd'));
    echo '[OK] 清空查询缓存成功!'."\n";
}

saveSetting('checktime', date("Y-m-d H:i:s"));

echo '[OK] '.date("Y-m-d H:i:s")."\n";
