# QQ-API接口文档

- [QQ-API接口文档](#qq-api接口文档)
  - [获取COOKIE接口](#获取cookie接口)
  - [获取clientkey接口](#获取clientkey接口)
  - [获取空间说说列表](#获取空间说说列表)
  - [获取空间日志列表](#获取空间日志列表)
  - [查询空间人气](#查询空间人气)
  - [查询已开通权益](#查询已开通权益)
  - [查询QQ等级信息](#查询qq等级信息)
  - [查询QQ昵称](#查询qq昵称)


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
