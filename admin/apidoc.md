# QQ-API接口文档

- [QQ-API接口文档](#qq-api接口文档)
  - [获取COOKIE接口](#获取cookie接口)
  - [获取clientkey接口](#获取clientkey接口)
  - [获取QQ互联登录授权code](#获取QQ互联登录授权code)
  - [获取空间说说列表](#获取空间说说列表)
  - [获取空间日志列表](#获取空间日志列表)
  - [查询空间人气](#查询空间人气)
  - [查询已开通权益](#查询已开通权益)
  - [查询QQ等级信息](#查询qq等级信息)
  - [查询QQ昵称](#查询qq昵称)
  - [ICP备案查询](#ICP备案查询)
  - [QQ音乐下载链接解析](#QQ音乐下载链接解析)


## 获取COOKIE接口

*说明：该接口只能获取已登录的QQ的COOKIE，调用方需自行实现COOKIE缓存与检测逻辑，不要每次使用都重新获取。*

请求URL：

> /api.php?act=getcookie

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述                                      |
| ------ | ---- | ------ | ----------------------------------------- |
| key    | 是   | string | 获取COOKIE密钥                            |
| type   | 是   | string | COOKIE类型编码（[参考下表](#cookietype)） |
| uin    | 否   | string | 指定QQ（留空为随机）                      |

返回示例：

```
{
    "code":0,
    "uin":"123456",
    "type":"qzone",
    "cookie":"skey=@ABCD...."
}
```

异常返回示例：

```
{
    "code":-1,
    "msg":"QQ状态不正常"
}
```

返回参数说明：

| 参数名 | 类型   | 描述                 |
| ------ | ------ | -------------------- |
| code   | int    | 0 是成功，其他是失败 |
| msg    | string | 失败原因             |
| uin    | string | QQ号码               |
| type   | string | COOKIE类型编码       |
| cookie | string | COOKIE内容           |

<span id = "cookietype">支持的COOKIE类型</span>：

| COOKIE类型编码 | COOKIE类型描述 |
| -------------- | -------------- |
| qzone          | QQ空间         |
| qun            | QQ群           |
| vip            | QQ会员         |
| ti             | 手机QQ         |
| video          | 腾讯视频       |
| music          | QQ音乐         |
| wenwen         | 腾讯问问       |
| dongman        | 腾讯动漫       |
| weishi         | 微视           |
| qcloud         | 腾讯云         |
| tenpay         | 财付通         |

## 获取clientkey接口

请求URL：

> /api.php?act=getclientkey

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述                 |
| ------ | ---- | ------ | -------------------- |
| key    | 是   | string | 获取COOKIE密钥       |
| uin    | 否   | string | 指定QQ（留空为随机） |

返回示例：

```
{
    "code":0,
    "uin":"123456",
    "clientkey":"ABCDEF....."
}
```

返回参数说明：

| 参数名    | 类型   | 描述                 |
| --------- | ------ | -------------------- |
| code      | int    | 0 是成功，其他是失败 |
| msg       | string | 失败原因             |
| uin       | string | QQ号码               |
| clientkey | string | clientkey内容        |

## 获取QQ互联登录授权code

*说明：可用此code获取任意已对接QQ快捷登录站点的cookie*

请求URL：

> /api.php?act=getoauthcode

请求方式：POST

请求参数：

| 参数名       | 必填 | 类型   | 描述                 |
| ------------ | ---- | ------ | -------------------- |
| key          | 是   | string | 获取COOKIE密钥       |
| client_id    | 是   | string | QQ互联应用ID         |
| redirect_uri | 是   | string | QQ互联回调地址       |
| uin          | 否   | string | 指定QQ（留空为随机） |

返回示例：

```
{
    "code":0,
    "uin":"123456",
    "oauthcode":"4297F44B13955235245B2497399D7A93"
}
```

返回参数说明：

| 参数名    | 类型   | 描述                 |
| --------- | ------ | -------------------- |
| code      | int    | 0 是成功，其他是失败 |
| msg       | string | 失败原因             |
| uin       | string | QQ号码               |
| oauthcode | string | 登录授权code         |

## 获取空间说说列表

请求URL：

> /api.php?act=getshuoshuo

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述          |
| ------ | ---- | ------ | ------------- |
| key    | 是   | string | API接口密钥   |
| uin    | 是   | string | QQ号码        |
| page   | 否   | int    | 页码，默认是1 |

返回示例：

```
{
    "code":0,
    "from":"online",
    "data":[
        {
            "tid":"a772225b5b10ff5f3a750e00",
            "content":"1.22活动盛大来袭，关注～",
            "time":1610551387
        },
        {
            "tid":"a772225bc08f405fc4660400",
            "content":"项目诚招店主，有意者来谈。",
            "time":1598066624
        },
        {
            "tid":"a772225b4cc79f5bfd790800",
            "content":"海底月你捞不起，心上人你不可及。",
            "time":1537197900
        }
    ]
}
```

返回参数说明：

| 参数名         | 类型   | 描述                 |
| -------------- | ------ | -------------------- |
| code           | int    | 0 是成功，其他是失败 |
| msg            | string | 失败原因             |
| data           | array  | 说说列表             |
| data[].tid     | string | 说说ID               |
| data[].content | string | 说说内容             |
| data[].time    | int    | 说说发表时间         |

## 获取空间日志列表

请求URL：

> /api.php?act=getrizhi

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述          |
| ------ | ---- | ------ | ------------- |
| key    | 是   | string | API接口密钥   |
| uin    | 是   | string | QQ号码        |
| page   | 否   | int    | 页码，默认是1 |

返回示例：

```
{
    "code": 0,
    "from": "online",
    "data": [
        {
            "id": 1344993982,
            "title": "我的日志标题",
            "time": "2013-08-15 09:26"
        },
        {
            "id": 1344148414,
            "title": "我的Qzone第一天",
            "time": "2012-08-05 14:33"
        }
    ]
}
```

返回参数说明：

| 参数名         | 类型   | 描述                 |
| -------------- | ------ | -------------------- |
| code           | int    | 0 是成功，其他是失败 |
| msg            | string | 失败原因             |
| data           | array  | 日志列表             |
| data[].id      | string | 日志ID               |
| data[].content | string | 日志内容             |
| data[].time    | string | 日志发表时间         |

## 查询空间人气

请求URL：

> /api.php?act=getvisitcount

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述        |
| ------ | ---- | ------ | ----------- |
| key    | 是   | string | API接口密钥 |
| uin    | 是   | string | QQ号码      |

返回示例：

```
{
    "code": 0,
    "todaycount": 8,
    "totalcount": 10471,
    "from": "online"
}
```

返回参数说明：

| 参数名     | 类型   | 描述                 |
| ---------- | ------ | -------------------- |
| code       | int    | 0 是成功，其他是失败 |
| msg        | string | 失败原因             |
| todaycount | int    | 今日访问量           |
| totalcount | int    | 总访问量             |

## 查询已开通权益

请求URL：

> /api.php?act=getprivilege

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述        |
| ------ | ---- | ------ | ----------- |
| key    | 是   | string | API接口密钥 |
| uin    | 是   | string | QQ号码      |

返回示例：

```
{
    "code": 0,
    "data": ["svip","growth","qqlevel"],
    "from": "online"
}
```

返回参数说明：

| 参数名 | 类型   | 描述                 |
| ------ | ------ | -------------------- |
| code   | int    | 0 是成功，其他是失败 |
| msg    | string | 失败原因             |
| data   | array  | 已开通权益列表       |

## 查询QQ等级信息

请求URL：

> /api.php?act=getqqlevel

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述        |
| ------ | ---- | ------ | ----------- |
| key    | 是   | string | API接口密钥 |
| uin    | 是   | string | QQ号码      |

返回示例：

```
{
    "code": 0,
    "data": {
        "iMobileQQOnlineTime": "0",
        "iSqqLevel": "0",
        "SVIPStar": "0",
        "iBigClubGrowth": "1",
        "iBigClubVipFlag": "0",
        "iCmshowActive": "0",
        "iMobileGameOnline": "0",
        "iMobileQQOnline": "0",
        "iTotalActiveDay": "6971",
        "iVipLevel": "9",
        "iVipSpeedRate": "0",
        "iYearVip": "1",
        "iPCQQOnline": "1",
        "iRealDays": "0.5",
        "iTotalDays": "8.9",
        "iBigClubLevel": "1",
        "iCostMs": "74",
        "iPCSafeOnline": "0",
        "iSqq": "0",
        "sNickName": "我的昵称",
        "speedStarv2": "0",
        "iContinueLogin": "0",
        "iMaxLvlTotalDays": "12.1",
        "iNextLevelDay": "77",
        "iSmallWorld": "0",
        "iSqqSpeedRate": "0",
        "speedStar": "0",
        "WeishiVideoview": "0",
        "iAddFriend": "0",
        "iBaseDays": "0.5",
        "iSpeedType": "0",
        "sFaceUrl": "http://thirdqq.qlogo.cn/g",
        "iBigClubSpeed": "-20",
        "iMaxLvlRealDays": "1.6",
        "iMedal": "0",
        "iPCQQOnlineTime": "18000",
        "iQzoneState": "0",
        "iSVip": "1",
        "speedStarv3": "0",
        "Lxby": "0",
        "QzoneVisitor": "0",
        "iMqing": "0",
        "iSvrDays": "0.0",
        "iVip": "1",
        "iYearBigClubFlag": "0",
        "iDailySign": "0",
        "iNoHideOnline": "0",
        "iNoHideOnlineTime": "0",
        "iQQLevel": "78",
        "iQQSportStep": "0"
    },
    "from": "online"
}
```

返回参数说明：

| 参数名               | 类型   | 描述                 |
| -------------------- | ------ | -------------------- |
| code                 | int    | 0 是成功，其他是失败 |
| msg                  | string | 失败原因             |
| data                 | array  | QQ等级信息           |
| data.sNickName       | string | QQ昵称               |
| data.sFaceUrl        | string | QQ头像               |
| data.iQQLevel        | string | QQ等级               |
| data.iRealDays       | string | 今日成长天数         |
| data.iTotalActiveDay | string | 活动总天数           |
| data.iVip            | string | 是否VIP              |
| data.iSVip           | string | 是否SVIP             |
| data.iVipLevel       | string | VIP等级              |

## 查询QQ昵称

请求URL：

> /api.php?act=getqqnick

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述        |
| ------ | ---- | ------ | ----------- |
| key    | 是   | string | API接口密钥 |
| uin    | 是   | string | QQ号码      |

返回示例：

```
{
    "code": 0,
    "nick": "我的昵称",
    "from": "cache"
}
```

返回参数说明：

| 参数名 | 类型   | 描述                 |
| ------ | ------ | -------------------- |
| code   | int    | 0 是成功，其他是失败 |
| msg    | string | 失败原因             |
| nick   | string | QQ昵称               |

## ICP备案查询

请求URL：

> /api.php?act=icpquery

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述        |
| ------ | ---- | ------ | ----------- |
| key    | 是   | string | API接口密钥 |
| domain | 是   | string | 域名        |

返回示例：

```
{
    "code": 0,
    "data": {
        "CompanyName": "深圳市腾讯计算机系统有限公司",
        "CompanyType": "企业",
        "DomainIcpNum": "粤B2-20090059-5",
        "WebsiteName": "-",
        "WebsiteHomeUrl": "-",
        "AuditTime": "2022-09-06",
        "Domain": "qq.com",
        "InTencent": true
    },
    "from": "online"
}
```

返回参数说明：

| 参数名            | 类型   | 描述                 |
| ----------------- | ------ | -------------------- |
| code              | int    | 0 是成功，其他是失败 |
| msg               | string | 失败原因             |
| data.Domain       | string | 域名                 |
| data.DomainIcpNum | string | 备案号               |
| data.CompanyName  | string | 主办单位名称         |
| data.CompanyType  | string | 主办单位性质         |
| data.AuditTime    | string | 审核日期             |
| data.InTencent    | bool   | 是否接入腾讯云       |

## QQ音乐下载链接解析

*如需解析高品质音乐，需开通绿钻豪华版*

请求URL：

> /api.php?act=getmusic

请求方式：POST

请求参数：

| 参数名 | 必填 | 类型   | 描述        |
| ------ | ---- | ------ | ----------- |
| key    | 是   | string | API接口密钥 |
| id     | 否   | int    | 音乐id      |
| mid    | 否   | string | 音乐mid     |

返回示例：

```
{
    "code": 0,
    "data": {
        "id": 107192078,
        "mid": "003OUlho2HcRHC",
        "title": "告白气球",
        "subtitle": "",
        "album": "周杰伦的床边故事",
        "author": "周杰伦",
        "urls": [
            {
                "type": "flac",
                "url": "http://ws.stream.qqmusic.qq.com/...",
                "size": 47089150
            },
            {
                "type": "320mp3",
                "url": "http://ws.stream.qqmusic.qq.com/...",
                "size": 8626883
            },
            {
                "type": "192aac",
                "url": "http://ws.stream.qqmusic.qq.com/...",
                "size": 5215383
            },
            {
                "type": "128mp3",
                "url": "http://ws.stream.qqmusic.qq.com/...",
                "size": 3450877
            }
        ]
    },
    "from": "online"
}
```

返回参数说明：

| 参数名           | 类型   | 描述                                  |
| ---------------- | ------ | ------------------------------------- |
| code             | int    | 0 是成功，其他是失败                  |
| msg              | string | 失败原因                              |
| data.id          | int    | 音乐id                                |
| data.mid         | string | 音乐mid                               |
| data.title       | string | 音乐名称                              |
| data.album       | string | 专辑名称                              |
| data.author      | string | 作者名称                              |
| data.urls        | array  | 下载链接列表（音质由高到低排序）      |
| data.urls[].type | string | 音质类型（flac/320mp3/192aac/128mp3） |
| data.urls[].url  | string | 下载链接                              |
| data.urls[].size | int    | 文件大小                              |
