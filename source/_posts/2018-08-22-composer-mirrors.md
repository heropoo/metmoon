---
layout: post
title:  "Composer官方镜像太慢或者被墙无法使用时的几种解决方案"
date:   2018-08-2 12:11:06
author: "Heropoo"
categories: 
    - Composer
    - PHP
tags:
    - Composer
    - PHP
excerpt: "Composer官方镜像太慢或者被墙无法使用时的几种解决方案"
---
Composer官方镜像太慢或者被墙无法使用时的几种解决方案

### 使用代理
被墙使用国外代理上网，总是一种行之有效的方法。加入你使用shadowsocks代理，开启之后默认的本地端口是1080。只要设置一个环境变量`http_proxy`就可以使用了。

Mac OS / Linux 终端
```bash
export http_proxy=127.0.0.1:1080
```
windows cmd命令行
```cmd
set http_proxy=127.0.0.1:1080
```

这样就可以了，愉快的下载各种包吧~

### 使用国内镜像地址
> * Composer中文网提供的镜像地址： https://packagist.phpcomposer.com
> * LaravelChina社区提供的镜像地址： https://packagist.laravel-china.org

镜像使用方法，请参考这里 [https://pkg.phpcomposer.com/#how-to-use-packagist-mirror](https://pkg.phpcomposer.com/#how-to-use-packagist-mirror)
