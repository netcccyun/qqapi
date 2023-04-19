<?php

namespace lib;

class QQCheck
{
    //检测cookie是否有效
    public static function checkCookie($type, $uin, $cookie)
    {
        switch ($type) {
            case 'qzone':
                return self::checkQzoneCookie($uin, $cookie);
                break;
            case 'vip':
                return self::checkVipCookie($uin, $cookie);
                break;
            default:
                return false;
                break;
        }
    }

    private static function checkQzoneCookie($uin, $cookie)
    {
        $pskey = getSubstr($cookie, 'p_skey=', ';');
        if (!$pskey) return false;
        $gtk = getGTK($pskey);
        $url = 'https://user.qzone.qq.com/proxy/domain/r.qzone.qq.com/cgi-bin/user/qzone_cgi_msg_getcnt2?uin=' . $uin . '&bm=0800950000008001&v=1&g_tk=' . $gtk . '&g=0.291287' . time();
        $data = get_curl($url, 0, 'http://cnc.qzs.qq.com/qzone/v6/setting/profile/profile.html', $cookie);
        preg_match('/\_Callback\((.*?)\);/is', $data, $json);
        $arr = json_decode($json[1], true);
        if (isset($arr['error']) && $arr['error'] == 4004) {
            return false;
        } else {
            return true;
        }
    }

    private static function checkVipCookie($uin, $cookie)
    {
        $pskey = getSubstr($cookie, 'p_skey=', ';');
        if (!$pskey) return false;
        $gtk = getGTK($pskey);
        $url = 'https://club.vip.qq.com/api/trpc/qid_mall_server/GetMainPage?g_tk=' . $gtk;
        $data = get_curl($url, '{}', 'https://club.vip.qq.com/qid/mine', $cookie);
        if (strpos($data, 'https://ui.ptlogin2.qq.com/')) {
            return false;
        } else {
            return true;
        }
    }
}
