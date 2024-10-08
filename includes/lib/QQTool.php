<?php

namespace lib;

class QQTool
{
    private $uin;
    private $cookie;
    private $gtk;
    private $skey;
    public $cookiezt = false;

    public function __construct($uin, $cookie)
    {
        $this->uin = $uin;
        $this->cookie = $cookie;
        if (strpos($cookie, 'p_skey=')) {
            $pskey = getSubstr($cookie, 'p_skey=', ';');
            $this->gtk = getGTK($pskey);
        } else {
            $this->skey = getSubstr($cookie, 'skey=', ';');
            $this->gtk = getGTK($this->skey);
        }
    }

    //获取说说列表
    public function getshuoshuo($touin, $page = 1)
    {
        $pos = ($page - 1) * 10;
        $url = 'https://user.qzone.qq.com/proxy/domain/taotao.qq.com/cgi-bin/emotion_cgi_msglist_v6?uin=' . $touin . '&ftype=0&sort=0&pos=' . $pos . '&num=10&replynum=0&code_version=1&format=json&need_private_comment=0&g_tk=' . $this->gtk;
        $data = get_curl($url, 0, 'https://user.qzone.qq.com/' . $touin, $this->cookie);
        $data = mb_convert_encoding($data, "UTF-8", "UTF-8");
        $arr = json_decode($data, true);
        if (isset($arr['code']) && $arr['code'] == 0) {
            $list = [];
            foreach ($arr['msglist'] as $row) {
                $content = $row['content'] ? $row['content'] : $row['rt_con']['content'];
                $list[] = ['tid' => $row['tid'], 'content' => $content, 'time' => $row['created_time']];
            }
            $result = array("code" => 0, "data" => $list);
        } elseif ($arr['code'] == -3000) {
            $this->cookiezt = true;
            $result = array("code" => -1, "subcode"=>101, "msg" => '获取最新说说失败！' . $arr['message']);
        } elseif ($arr['code'] == -10031) {
            $result = array("code" => -1, "subcode"=>103, "msg" => '当前QQ的空间未开放访问权限！');
        } elseif (isset($arr['message'])) {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取最新说说失败！' . $arr['message']);
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取最新说说失败！请稍候再试');
        }
        return $result;
    }

    //获取说说列表2
    public function getshuoshuo_bak($touin, $page = 1)
    {
        $pos = ($page - 1) * 10;
        $url = 'https://mobile.qzone.qq.com/list?g_tk=' . $this->gtk . '&res_attach=att%3Doffset%253D' . $pos . '&format=json&list_type=shuoshuo&action=0&res_uin=' . $touin . '&count=10';
        $data = get_curl($url, 0, 'https://h5.qzone.qq.com/mqzone/profile' . $touin, $this->cookie);
        $data = mb_convert_encoding($data, "UTF-8", "UTF-8");
        $arr = json_decode($data, true);
        if (isset($arr['code']) && $arr['code'] == 0) {
            $list = [];
            foreach ($arr['data']['vFeeds'] as $row) {
                $content = $row['summary']['summary'] ? $row['summary']['summary'] : '无文字内容';
                $list[] = ['tid' => $row['id']['cellid'], 'content' => $content, 'time' => $row['comm']['time']];
            }
            $result = array("code" => 0, "data" => $list);
        } elseif ($arr['code'] == -3000) {
            $this->cookiezt = true;
            $result = array("code" => -1, "subcode"=>101, "msg" => '获取最新说说失败！' . $arr['message']);
        } elseif ($arr['code'] == -10031) {
            $result = array("code" => -1, "subcode"=>103, "msg" => '当前QQ的空间未开放访问权限！');
        } elseif (isset($arr['message'])) {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取最新说说失败！' . $arr['message']);
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取最新说说失败！请稍候再试');
        }
        return $result;
    }

    //获取日志列表
    public function getrizhi($touin, $page = 1)
    {
        $pos = ($page - 1) * 10;
        $url = 'https://user.qzone.qq.com/proxy/domain/b.qzone.qq.com/cgi-bin/blognew/get_abs?hostUin=' . $touin . '&uin=' . $this->uin . '&blogType=0&cateName=&cateHex=&statYear=2010&reqInfo=5&pos=' . $pos . '&num=10&sortType=0&absType=0&source=0&rand=&ref=qzone&g_tk=' . $this->gtk . '&verbose=1&format=json';
        $data = get_curl($url, 0, 'https://user.qzone.qq.com/' . $touin, $this->cookie);
        $data = mb_convert_encoding($data, "UTF-8", "GBK");
        $arr = json_decode($data, true);
        if (isset($arr['code']) && $arr['code'] == 0) {
            $list = [];
            foreach ($arr['data']['list'] as $row) {
                $list[] = ['id' => $row['blogId'], 'title' => $row['title'], 'time' => $row['pubTime']];
            }
            $result = array("code" => 0, "data" => $list);
        } elseif ($arr['code'] == -3000) {
            $this->cookiezt = true;
            $result = array("code" => -1, "subcode"=>101, "msg" => '获取最新日志失败！' . $arr['message']);
        } elseif ($arr['code'] == -10031) {
            $result = array("code" => -1, "subcode"=>103, "msg" => '当前QQ的空间未开放访问权限！');
        } elseif (isset($arr['message'])) {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取最新日志失败！' . $arr['message']);
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取最新日志失败！请稍候再试');
        }
        return $result;
    }

    //获取空间人气
    public function getvisitcount($uin)
    {
        $url = 'https://user.qzone.qq.com/proxy/domain/g.qzone.qq.com/cgi-bin/friendshow/cgi_get_visitor_simple?uin='.$uin.'&mask=1&g_tk=' . $this->gtk;
        $data = get_curl($url, 0, 'https://user.qzone.qq.com/' . $uin, $this->cookie);
        $arr = jsonp_decode($data, true);
        if (isset($arr['code']) && $arr['code'] == 0) {
            $todaycount = $arr['data']['modvisitcount'][0]['todaycount'];
            $totalcount = $arr['data']['modvisitcount'][0]['totalcount'];
            $result = array("code" => 0, "todaycount" => $todaycount, "totalcount" => $totalcount);
        } elseif (isset($arr['error'])) {
            if($arr['error']['type'] == -3){
                $this->cookiezt = true;
                $result = array("code" => -1, "subcode"=>101, "msg" => '获取空间人气失败！COOKIE已失效');
            }elseif($arr['error']['type'] == -6){
                $result = array("code" => -1, "subcode"=>103, "msg" => '当前QQ的空间未开放访问权限！');
            }else{
                $result = array("code" => -1, "subcode"=>102, "msg" => '获取空间人气失败！'.$arr['error']['msg']);
            }
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取空间人气失败！请稍候再试');
        }
        return $result;
    }

    //获取已开通权益
    public function getprivilege($uin)
    {
        $url = 'https://club.vip.qq.com/guestprivilege?friend=' . $uin;
        $ua = 'Mozilla/5.0 (Linux; Android 12; M2011K2C Build/SKQ1.211006.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/98.0.4758.102 MQQBrowser/6.2 TBS/046317 Mobile Safari/537.36 V1_AND_SQ_8.9.23_3584_YYB_D QQ/8.9.23.9865 NetType/4G WebP/0.3.0 AppId/537143495';
        $data = get_curl($url, 0, 0, $this->cookie, 0, $ua);
        $data = getSubstr($data, 'window.__INITIAL_STATE__=', ';(function(){');
        if ($data) {
            $arr = json_decode($data, true);
            if (isset($arr['privilege']['guestPrivileges'])) {
                $privilege = [];
                foreach ($arr['privilege']['guestPrivileges'] as $row) {
                    $privilege[] = $row['privilege'];
                }
                //preg_match_all("!class=\"(.*?)\"!",$data2,$match);
                $result = array("code" => 0, "data" => $privilege);
            } else {
                $result = array("code" => -1, "subcode"=>102, "msg" => '获取已开通权益失败！');
            }
        } elseif (strpos($data, '/html/relogin.html?')) {
            $this->cookiezt = true;
            $result = array("code" => -1, "subcode"=>101, "msg" => '获取已开通权益失败！COOKIE已失效');
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取已开通权益失败！请稍候再试');
        }
        return $result;
    }

    //获取QQ等级信息
    public function getqqlevel($uin)
    {
        $skey = getSubstr($this->cookie, 'skey=@', ';');
        $body = json_encode(['sClientIp'=>'127.0.0.1', 'sSessionKey'=>$skey, 'iKeyType'=>1, 'iAppId'=>0, 'iUin'=>$uin]);
        $url = 'https://club.vip.qq.com/api/vip/getQQLevelInfo?g_tk='.$this->gtk.'&requestBody='.urlencode($body);
        $ua = 'Mozilla/5.0 (Linux; Android 12; M2011K2C Build/SKQ1.211006.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/98.0.4758.102 MQQBrowser/6.2 TBS/046317 Mobile Safari/537.36 V1_AND_SQ_8.9.23_3584_YYB_D QQ/8.9.23.9865 NetType/4G WebP/0.3.0 AppId/537143495';
        $referer = 'https://club.vip.qq.com/card/friend?_wv=16778247&_wwv=68&_wvx=10&_proxy=1&_proxyByURL=1&qq='.$uin;
        $data = get_curl($url, 0, $referer, $this->cookie, 0, $ua);
        $arr = json_decode($data, true);
        if (isset($arr['ret']) && $arr['ret'] == 0) {
            $result = array("code" => 0, "data" => $arr['data']['mRes']);
        } elseif (strpos($data, 'https://ui.ptlogin2.qq.com/')) {
            $this->cookiezt = true;
            $result = array("code" => -1, "subcode"=>101, "msg" => '获取QQ等级信息失败！COOKIE已失效');
        } elseif ($data) {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取QQ等级信息失败！' . $data);
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取QQ等级信息失败！请稍候再试');
        }
        return $result;
    }

    //获取QQ昵称
    public function getqqnick($uin)
    {
        $url = 'https://h5.qzone.qq.com/proxy/domain/r.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?get_nick=1&uins=' . $uin . '&g_tk=' . $this->gtk;
        $data = get_curl($url, 0, 'https://user.qzone.qq.com/' . $uin, $this->cookie);
		$data = mb_convert_encoding($data, "UTF-8", "GBK");
        $arr = jsonp_decode($data, true);
        if (isset($arr[$uin])) {
            $result = array("code" => 0, "nick" => $arr[$uin][6]);
        } elseif (isset($arr['error'])) {
            if($arr['error']['type'] == 'need login'){
                $this->cookiezt = true;
                $result = array("code" => -1, "subcode"=>101, "msg" => '获取QQ昵称失败！COOKIE已失效');
            }else{
                $result = array("code" => -1, "subcode"=>102, "msg" => '获取QQ昵称失败！'.$arr['error']['msg']);
            }
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '获取QQ昵称失败！请稍候再试');
        }
        return $result;
    }

    //ICP备案查询
    public function icpquery($domain)
    {
        $uin = getSubstr($this->cookie, 'uin=', ';');
        $uin = getUin($uin);
        $url = 'https://console.cloud.tencent.com/cgi/capi?cmd=DescribeIcpDomainInfo&action=delegate&serviceType=ba&secure=1&version=3&dictId=2163&sts=1&t='.time().'123&uin='.$uin.'&ownerUin='.$uin.'&csrfCode=' . $this->gtk;
        $post = '{"serviceType":"ba","regionId":1,"data":{"Version":"2020-07-20","RequestClient":"PC","Type":"DOMAIN","Value":"'.$domain.'"},"cmd":"DescribeIcpDomainInfo"}';
        $data = get_curl($url, $post, 'https://console.cloud.tencent.com/beian/search', $this->cookie, 0, 0, 0, ['Content-Type: application/json; charset=UTF-8']);
        $arr = json_decode($data, true);
        if (isset($arr['code']) && $arr['code'] == 0) {
            if($arr['data']['code'] == 0){
                $resp = $arr['data']['data']['Response'];
                if(!$resp['Empty'] && !empty($resp['Results'])){
                    $result = array("code" => 0, "data" => $resp['Results'][0]);
                }else{
                    $result = array("code" => 0, "data" => null);
                }
            }else{
                $result = array("code" => -1, "subcode"=>102, "msg" => '备案查询失败！请稍候再试');
            }
        } elseif (isset($arr['code']) && $arr['code'] == 50) {
            $this->cookiezt = true;
            $result = array("code" => -1, "subcode"=>101, "msg" => '备案查询失败！COOKIE已失效');
        } elseif (isset($arr['msg'])) {
            $result = array("code" => -1, "subcode"=>102, "msg" => '备案查询失败！' . $arr['msg']);
        } else {
            $result = array("code" => -1, "subcode"=>102, "msg" => '备案查询失败！请稍候再试');
        }
        return $result;
    }

    public function getmusic($id, $mid){
        $qqmusic_key = getSubstr($this->cookie, 'qqmusic_key=', ';');
        $this->gtk = getGTK($qqmusic_key);
        if(!empty($id)){
            $param = ['song_id'=>$id, 'song_type'=>0];
        }else{
            $param = ['song_mid'=>$mid, 'song_type'=>0];
        }
        $url = 'https://u6.y.qq.com/cgi-bin/musicu.fcg';
        $param = [
            'comm' => ['cv'=>0,'ct'=>24,'format'=>'json','inCharset'=>'utf-8','outCharset'=>'utf-8','notice'=>0,'platform'=>'yqq','needNewCode'=>1,'uin'=>$this->uin,'g_tk'=>$this->gtk],
            'req_0' => ['module'=>'music.paycenterapi.LoginStateVerificationApi','method'=>'GetChargeAccount','param'=>['appid'=>'mlive']],
            'req_1' => ['module'=>'music.pf_song_detail_svr','method'=>'get_song_detail','param'=>$param],
        ];
        $post = json_encode($param);
        $data = get_curl($url, $post, 'https://y.qq.com/', $this->cookie, 0, 0, 0, ['Content-Type: application/json']);
        $arr = json_decode($data, true);
        if(isset($arr['req_0']['code']) && $arr['req_0']['code'] == 0){
            $arr = $arr['req_1'];
            if(isset($arr['code']) && $arr['code'] == 0){
                $arr = $arr['data']['track_info'];
                $singers = [];
                foreach ($arr['singer'] as $singer) {
                    $singers[] = $singer['title'];
                }
                $info = [
                    'id' => $arr['id'],
                    'mid' => $arr['mid'],
                    'title' => $arr['title'],
                    'subtitle' => $arr['subtitle'],
                    'album' => $arr['album']['title'],
                    'author' => implode(',', $singers),
                ];
                $urls = $this->get_music_url($arr['mid'], $arr['type'], $arr['file']);
                if($urls){
                    $info['urls'] = $urls;
                    $result = array("code" => 0, "data" => $info);
                }else{
                    $result = array("code" => -1, "subcode"=>102, "msg" => '获取音乐下载链接失败，可能需要VIP身份');
                }
            }else{
                $result = array("code" => -1, "subcode"=>102, "msg" => '获取音乐信息失败！请稍候再试');
            }
        }else{
            $this->cookiezt = true;
            $result = array("code" => -1, "subcode"=>101, "msg" => '获取音乐信息失败！COOKIE已失效');
        }
        return $result;
    }

    private function get_music_url($mid, $type, $file){
        $type_list = [
            ['flac', 999, 'F000', '.flac'],
            ['320mp3', 320, 'M800', '.mp3'],
            ['192aac', 192, 'C600', '.m4a'],
            ['128mp3', 128, 'M500', '.mp3'],
        ];
        $guid=(string)rand(111111111,999999999);
        $url = 'https://u6.y.qq.com/cgi-bin/musicu.fcg';
        $param = [
            'comm' => ['cv'=>0,'ct'=>24,'format'=>'json','inCharset'=>'utf-8','outCharset'=>'utf-8','notice'=>0,'platform'=>'yqq','needNewCode'=>1,'uin'=>$this->uin,'g_tk'=>$this->gtk],
            'req_0' => ['module'=>'vkey.GetVkeyServer','method'=>'CgiGetVkey','param'=>['checklimit'=>0,'ctx'=>1,'downloadfrom'=>0,'guid'=>$guid,'songmid'=>[],'filename'=>[],'songtype'=>[],'uin'=>$this->uin,'loginflag'=>1,'platform'=>'20']],
        ];
        foreach($type_list as $row){
            $param['req_0']['param']['songmid'][] = $mid;
            $param['req_0']['param']['filename'][] = $row[2].$file['media_mid'].$row[3];
            $param['req_0']['param']['songtype'][] = $type;
        }
        $post = json_encode($param);
        $data = get_curl($url, $post, 'https://y.qq.com/', $this->cookie, 0, 0, 0, ['Content-Type: application/json']);
        $arr = json_decode($data, true);
        $arr = $arr['req_0'];
        if(isset($arr['code']) && $arr['code']==0){
            $sip = $arr['data']['sip'][0];
            if(!$sip) $sip = 'http://ws.stream.qqmusic.qq.com/';
            $url_list = [];
            foreach($type_list as $index => $row){
                if(!empty($arr['data']['midurlinfo'][$index]['purl'])){
                    $url_list[] = ['type'=>$row[0], 'url'=>$sip.$arr['data']['midurlinfo'][$index]['purl'], 'size'=>$file['size_'.$row[0]]];
                }
            }
            if(empty($url_list)) return false;
            return $url_list;
        }
        return false;
    }
}
