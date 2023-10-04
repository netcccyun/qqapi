<?php

namespace lib;

class QQLogin
{

    public $errmsg;
    public $uinlost = false;

    //已登录的QQ列表
    public function uinList()
    {
        global $conf;
        $url = 'http://'.$conf['server_ip'].':'.$conf['server_port'];
        $post = json_encode(['function'=>'GetAllQQlist', 'token'=>$conf['server_key']]);
        $res = $this->curl($url, $post);
        $arr = json_decode($res, true);
        if(isset($arr['code']) && $arr['code']==1){
            return $arr['data'];
        }
        return false;
    }

    public function getClientKey($uin)
    {
        global $conf;
        $url = 'http://'.$conf['server_ip'].':'.$conf['server_port'];
        $post = json_encode(['function'=>'GetClientKey', 'token'=>$conf['server_key'], 'param'=>['p1'=>$uin]]);
        $res = $this->curl($url, $post);
        $arr = json_decode($res, true);
        if(isset($arr['code']) && $arr['code']==1){
            return $arr['data']['clientkey'];
        }
        $this->uinlost = true;
        return false;
    }

    //判断QQ是否已上线
    public function checkLogin($uin)
    {
        $clientkey = $this->getClientKey($uin);
        if ($clientkey === false) {
            $this->errmsg = 'clientkey获取失败';
            return false;
        }
        $typeinfo = $this->getTypeInfo('connect');
        $daid = $typeinfo[0];
        $aid = $typeinfo[1];
        $surl = $typeinfo[2];
        $url = 'https://ssl.ptlogin2.qq.com/jump?ptlang=2052&clientuin=' . $uin . '&clientkey='.$clientkey.'&u1=' . urlencode($surl) . '&source=panelstar&pt_aid=' . $aid . '&daid=' . $daid . '&pt_3rd_aid=0';
        $res = $this->curl($url, 0, 0, 0, 1);
        if ($res['code'] == 302 && !empty($res['redirect_url']) && strpos($res['redirect_url'], '//www.qq.com/')===false) {
            return true;
        } else {
            $this->errmsg = '快捷登录失败';
        }
        return false;
    }

    //快速登录操作
    public function login($uin, $type, $pt_3rd_aid = '0')
    {
        $clientkey = $this->getClientKey($uin);
        if ($clientkey === false) {
            $this->errmsg = 'clientkey获取失败';
            return false;
        }
        $typeinfo = $this->getTypeInfo($type);
        if (!$typeinfo) {
            $this->errmsg = '无此登录类型';
            return false;
        }
        $daid = $typeinfo[0];
        $aid = $typeinfo[1];
        $surl = $typeinfo[2];

        $url = 'https://ssl.ptlogin2.qq.com/jump?ptlang=2052&clientuin=' . $uin . '&clientkey='.$clientkey.'&u1=' . urlencode($surl) . '&source=panelstar&pt_aid=' . $aid . '&daid=' . $daid . '&pt_3rd_aid='.$pt_3rd_aid;
        $res = $this->curl($url, 0, 0, 0, 1);
        if ($res['code'] == 302 && !empty($res['redirect_url']) && strpos($res['redirect_url'], '//www.qq.com/')===false) {
            $res = $this->curl($res['redirect_url'], 0, 0, 0, 1);
            $cookie = '';
            preg_match_all('/Set-Cookie: (.*);/iU', $res['header'], $matchs);
            foreach ($matchs[1] as $val) {
                if (substr($val, -1) == '=') continue;
                $cookie .= $val . '; ';
            }
            $cookie = substr($cookie, 0, -2);
            return $cookie;
        } else {
            $this->uinlost = true;
            $this->errmsg = '快捷登录失败';
        }
        return false;
    }

    //登录类型[daid,aid,surl]
    public function getTypeInfo($type)
    {
        switch ($type) {
            case 'qzone':
                return ['5', '549000912', 'https://qzs.qq.com/qzone/v5/loginsucc.html'];
                break;
            case 'qun':
                return ['73', '715030901', 'https://qun.qq.com/'];
                break;
            case 'vip':
                return ['18', '8000212', 'https://club.vip.qq.com/'];
                break;
            case 'ti':
                return ['338', '809041606', 'https://ti.qq.com/friendship_auth/index.html'];
                break;
            case 'tenpay':
                return ['120', '546000248', 'https://www.tenpay.com/v2/res/js/yui/build/login/ptlogin.shtml'];
                break;
            case 'connect':
                return ['383', '716027609', 'https://graph.qq.com/oauth2.0/login_jump'];
                break;
            default:
                return null;
                break;
        }
    }

    //QQ互联快速登录操作
    public function login3rd($uin, $type)
    {
        $typeinfo = $this->get3rdTypeInfo($type);
        if (!$typeinfo) {
            $this->errmsg = '无此登录类型';
            return false;
        }
        $client_id = $typeinfo[0];
        $redirect_uri = $typeinfo[1];

        $cookie = $this->login($uin, 'connect', $client_id);
        if ($cookie === false) return false;

        preg_match("/p_skey=(.*?);/", $cookie, $pskey);
        $url = 'https://graph.qq.com/oauth2.0/authorize';
        $post = 'response_type=code&client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=&state=STATE&switch=&from_ptlogin=1&src=1&update_auth=1&openapi=1010&g_tk=' . getGTK($pskey[1]) . '&auth_time=' . time() . '928&ui=A50D9994-9D16-4A65-B7FE-8D2A5D1134EC';
        $referer = 'https://graph.qq.com/oauth2.0/show?which=Login&display=pc&response_type=code&client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&state=STATE';
        $res = $this->curl($url, $post, $referer, $cookie, 1);
        if (preg_match("/Location: (.*?)\r\n/i", $res['header'], $match)) {
            parse_str(parse_url($match[1], PHP_URL_QUERY), $query_arr);
            if(isset($query_arr['code'])){
                return $this->login3rdByCode($type, $query_arr['code']);
            }elseif(isset($query_arr['error_description'])){
                $this->errmsg = '获取回调code失败，'.$query_arr['error_description'];
            }else{
                $this->errmsg = '获取回调code失败';
            }
        } else {
            $this->errmsg = '获取回调重定向地址失败';
        }
        return false;
    }

    //QQ互联登录类型[client_id,redirect_uri]
    public function get3rdTypeInfo($type)
    {
        switch ($type) {
            case 'video':
                return ['101483052', 'https://access.video.qq.com/user/auth_login?vappid=11059694&vsecret=fdf61a6be0aad57132bc5cdf78ac30145b6cd2c1470b0cfe&raw=1&type=qq&appid=101483052'];
                break;
            case 'music':
                return ['100497308', 'https://y.qq.com/portal/wx_redirect.html?login_type=1'];
                break;
            case 'wenwen':
                return ['101401138', 'https://wenwen.sogou.com/login/qq/user_info?business=wenwen'];
                break;
            case 'dongman':
                return ['101483258', 'https://ac.qq.com/loginSuccess.html?url=https://ac.qq.com/index?auth=1'];
                break;
            case 'weishi':
                return ['1101083114', 'https://h5.weishi.qq.com/weishi/account/login'];
                break;
            case 'qcloud':
                return ['101488968', 'https://cloud.tencent.com/login/qqAccessCallback'];
                break;
            default:
                return null;
                break;
        }
    }

    private function login3rdByCode($type, $code)
    {
        switch ($type) {
            case 'video':
                return $this->loginVideo($code);
                break;
            case 'music':
                return $this->loginMusic($code);
                break;
            case 'wenwen':
                return $this->loginWenwen($code);
                break;
            case 'dongman':
                return $this->loginDongman($code);
                break;
            case 'weishi':
                return $this->loginWeishi($code);
                break;
            case 'qcloud':
                return $this->loginQcloud($code);
                break;
        }
    }

    private function loginVideo($code)
    {
        $url = 'https://access.video.qq.com/user/auth_login?vappid=11059694&vsecret=fdf61a6be0aad57132bc5cdf78ac30145b6cd2c1470b0cfe&raw=1&type=qq&appid=101483052&code=' . $code;
        $referer = 'https://graph.qq.com/';
        $res = $this->curl($url, 0, $referer, 0, 1);
        if (preg_match("/var json = '(.*?)';/", $res['body'], $match)) {
            $arr = json_decode($match[1], true);
            if (isset($arr['next_refresh_time'])) {
                $cookie = '';
                preg_match_all('/Set-Cookie: (.*);/iU', $res['header'], $matchs);
                foreach ($matchs[1] as $val) {
                    if (substr($val, -1) == '=') continue;
                    $cookie .= $val . '; ';
                }
                $cookie = substr($cookie, 0, -2);
                return $cookie;
            } else {
                $this->errmsg = '登录腾讯视频失败，' . ($arr['msg'] ? $arr['msg'] : $match[1]);
            }
        } else {
            $this->errmsg = '登录腾讯视频失败，返回数据解析失败';
        }
        return false;
    }

    private function loginMusic($code)
    {
        $url = 'https://u.y.qq.com/cgi-bin/musicu.fcg';
        $post = '{"comm":{"g_tk":5381,"platform":"yqq","ct":24,"cv":0},"req":{"module":"QQConnectLogin.LoginServer","method":"QQLogin","param":{"code":"'.$code.'"}}}';
        $referer = 'https://y.qq.com/';
        $res = $this->curl($url, $post, $referer, 0, 1);
        $arr = json_decode($res['body'], true);
        if (isset($arr['code']) && $arr['code']==0 && $arr['req']['code']==0) {
            $cookie = '';
            preg_match_all('/set-cookie: (.*);/iU', $res['header'], $matchs);
            foreach ($matchs[1] as $val) {
                if (substr($val, -1) == '=') continue;
                $cookie .= $val . '; ';
            }
            $cookie = substr($cookie, 0, -2);
            return $cookie;
        } elseif(isset($arr['req']['msg'])) {
            $this->errmsg = '登录QQ音乐失败，' . $arr['req']['msg'];
        } else {
            $this->errmsg = '登录QQ音乐失败，返回数据解析失败';
        }
        return false;
    }

    private function loginWenwen($code)
    {
        $url = 'https://wenwen.sogou.com/login/qq/user_info?business=wenwen&return_url=aHR0cHM6Ly93ZW53ZW4uc29nb3UuY29tLw==&code='.$code.'&state='.time().'968';
        $referer = 'https://graph.qq.com/';
        $res = $this->curl($url, 0, $referer, 0, 1);
        $cookie = '';
        preg_match_all('/set-cookie: (.*);/iU', $res['header'], $matchs);
        foreach ($matchs[1] as $val) {
            if (substr($val, -1) == '=') continue;
            $cookie .= $val . '; ';
        }
        $cookie = substr($cookie, 0, -2);
        return $cookie;
    }

    private function loginDongman($code)
    {
        $url = 'https://ac.qq.com/User/qqInfo';
        $post = 'code='.$code;
        $referer = 'https://ac.qq.com/loginSuccess.html';
        $res = $this->curl($url, $post, $referer, 0, 1);
        $arr = json_decode($res['body'], true);
        if (isset($arr['status']) && $arr['status']==2) {
            $cookie = '';
            preg_match_all('/Set-Cookie: (.*);/iU', $res['header'], $matchs);
            foreach ($matchs[1] as $val) {
                if (substr($val, -1) == '=') continue;
                $cookie .= $val . '; ';
            }
            $cookie = substr($cookie, 0, -2);
            return $cookie;
        } else {
            $this->errmsg = '登录腾讯动漫失败，' . $res['body'];
        }
        return false;
    }

    private function loginWeishi($code)
    {
        $url = 'https://h5.weishi.qq.com/weishi/account/login?r_url=http%3A%2F%2Fh5.weishi.qq.com%2F&loginfrom=qc&code='.$code.'&state=state';
        $referer = 'https://graph.qq.com/';
        $res = $this->curl($url, 0, $referer, 0, 1);
        if ($res['code'] == 200) {
            $cookie = '';
            preg_match_all('/set-cookie: (.*);/iU', $res['header'], $matchs);
            foreach ($matchs[1] as $val) {
                if (substr($val, -1) == '=') continue;
                $cookie .= $val . '; ';
            }
            $cookie = substr($cookie, 0, -2);
            return $cookie;
        } else {
            $this->errmsg = '登录腾讯微视失败';
        }
        return false;
    }

    private function loginQcloud($code)
    {
        $url = 'https://cloud.tencent.com/login/qqAccessCallback?s_url=https%3A%2F%2Fconsole.cloud.tencent.com%2F&fwd_flag=7&code='.$code.'&state=state';
        $referer = 'https://graph.qq.com/';
        $res = $this->curl($url, 0, $referer, 0, 1);
        if ($res['code'] == 302) {
            $cookie = '';
            preg_match_all('/set-cookie: (.*);/iU', $res['header'], $matchs);
            foreach ($matchs[1] as $val) {
                if (substr($val, -1) == '=') continue;
                $cookie .= $val . '; ';
            }
            $cookie = substr($cookie, 0, -2);
            return $cookie;
        } else {
            $this->errmsg = '登录腾讯云失败';
        }
        return false;
    }

    private function curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: application/json";
        $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        if ($header) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($ret, 0, $headerSize);
            $body = substr($ret, $headerSize);
            $ret = array();
            $ret['code'] = $httpCode;
            $ret['header'] = $headers;
            $ret['body'] = $body;
            if($httpCode == 301 || $httpCode == 302){
                $ret['redirect_url'] = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
            }
        }
        curl_close($ch);
        return $ret;
    }
}
