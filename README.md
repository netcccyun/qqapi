# QQ-API系统

QQ-API是一款php开发的QQ相关API接口管理系统。本程序必须搭建在Windows服务器，并且同时需要在这台服务器上登录电脑版QQ。本程序可生成QQ空间、QQ会员、腾讯视频、QQ音乐等多种类型的COOKIE，并提供API接口，实现一些需要登录状态才能查询的接口。

### 功能特色

- 对接电脑版QQ快速登录接口，实现COOKIE保活
- 支持QQ掉线后邮件提醒
- 支持设置API查询缓存
- 后台可查看获取COOKIE日志与获取失败原因

### 使用方法

- 部署环境要求`PHP` >= 7.1、`MySQL` >= 5.5
- 上传后直接访问，按照提示安装，后台默认账号密码：admin/123456
- 在服务器登录电脑QQ，在 /admin/qqserver/.env 配置好端口和密钥，启动 qqserver.exe
- 在系统参数设置，配置登录服务器的IP、端口和密钥
- 添加QQ、添加COOKIE检测定时任务

- [接口文档](./admin/apidoc.md)

