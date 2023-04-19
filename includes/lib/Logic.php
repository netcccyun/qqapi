<?php

namespace lib;
use Exception;

class Logic
{
    //更新指定COOKIE
    public static function updateCookie($id){
        global $DB, $qqlogin_type, $qqlogin_type_3rd;
        $row = $DB->find('cookie', '*', ['id'=>$id]);
        if(!$row) throw new Exception('COOKIE不存在');
        $account = $DB->find('account', '*', ['id'=>$row['aid']]);
        if(!$account) throw new Exception('QQ不存在');
        if($account['status']!=1) throw new Exception('QQ状态不正常');
        $login = new \lib\QQLogin();
        if(array_key_exists($row['type'],$qqlogin_type_3rd)){
            $cookie = $login->login3rd($account['uin'], $row['type']);
        }elseif(array_key_exists($row['type'],$qqlogin_type)){
            $cookie = $login->login($account['uin'], $row['type']);
        }else{
            throw new Exception('无此登录类型');
        }
        if($cookie){
            $DB->update('cookie', ['content'=>$cookie, 'status'=>'1','addtime'=>'NOW()'], ['id'=>$id]);
            $DB->insert('log', ['uin'=>$account['uin'], 'type'=>$row['type'], 'action'=>'更新COOKIE', 'time'=>'NOW()']);
            return $cookie;
        }else{
            if($login->uinlost){
                $DB->update('account', ['status'=>'0'], ['id'=>$row['aid']]);
                self::noticeFail($row['aid']);
            }else{
                $DB->insert('log', ['uin'=>$account['uin'], 'type'=>$row['type'], 'action'=>'更新COOKIE', 'time'=>'NOW()', 'status'=>0, 'reason'=>$login->errmsg]);
            }
            throw new Exception($login->errmsg);
        }
    }

    //添加COOKIE
    public static function addCookie($aid, $type){
        global $DB, $qqlogin_type, $qqlogin_type_3rd;
        $account = $DB->find('account', '*', ['id'=>$aid]);
        if(!$account) throw new Exception('QQ不存在');
        if($account['status']!=1) throw new Exception('QQ状态不正常');
        $login = new \lib\QQLogin();
        if(array_key_exists($type,$qqlogin_type_3rd)){
            $cookie = $login->login3rd($account['uin'], $type);
        }elseif(array_key_exists($type,$qqlogin_type)){
            $cookie = $login->login($account['uin'], $type);
        }else{
            throw new Exception('无此登录类型');
        }
        if($cookie){
            $DB->insert('cookie', ['aid'=>$aid, 'type'=>$type, 'content'=>$cookie, 'addtime'=>'NOW()', 'usetime'=>'NOW()', 'checktime'=>'NOW()', 'status'=>'1']);
            $DB->insert('log', ['uin'=>$account['uin'], 'type'=>$type, 'action'=>'添加COOKIE', 'time'=>'NOW()']);
            return $cookie;
        }else{
            if($login->uinlost){
                $DB->update('account', ['status'=>'0'], ['id'=>$aid]);
                self::noticeFail($aid);
            }else{
                $DB->insert('log', ['uin'=>$account['uin'], 'type'=>$type, 'action'=>'添加COOKIE', 'time'=>'NOW()', 'status'=>0, 'reason'=>$login->errmsg]);
            }
            throw new Exception($login->errmsg);
        }
    }

    //QQ失效通知
    private static function noticeFail($id){
        global $DB,$conf;
        if($conf['mail_open'] == 1 && defined('IS_CRON')){
            $account = $DB->find('account', '*', ['id'=>$id]);
            if(!$account) return false;

            $mail_name = $conf['mail_recv']?$conf['mail_recv']:$conf['mail_name'];
            $mail_title = 'QQ:'.$account['uin'].' 失效提醒';
            $mail_content = '你在'.$conf['sitename'].'添加的QQ:'.$account['nickname'].'（'.$account['uin'].'）已失效，请及时更新！<br/><br/>'.date("Y-m-d H:i:s").'<br/>';
            send_mail($mail_name,$mail_title,$mail_content);
        }
    }
}