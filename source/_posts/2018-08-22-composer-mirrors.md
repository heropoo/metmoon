---
layout: post
title:  "Composer官方镜像太慢或者被墙无法使用时的几种解决方案"
date:   2018-08-2 12:11:06
author: "Heropoo"
categories: 
    - PHP
tags:
    - Composer
    - PHP
excerpt: "Composer官方镜像太慢或者被墙无法使用时的几种解决方案"
---
Composer官方镜像太慢或者被墙无法使用时的几种解决方案

### 使用代理
被墙使用国外代理上网，总是一种行之有效的方法。假如你使用shadowsocks代理，开启之后默认的本地端口是1080。只要设置两个环境变量`http_proxy`和`https_proxy`就可以使用了。

Mac OS / Linux 终端
```bash
export http_proxy=127.0.0.1:1080
export https_proxy=127.0.0.1:1080
```
windows cmd命令行
```cmd
set http_proxy=127.0.0.1:1080
set https_proxy=127.0.0.1:1080
```

这样就可以了，愉快的下载各种包吧~

### 使用国内镜像地址
> * 阿里云提供的镜像地址(推荐)：https://developer.aliyun.com/composer
> * cnpkg提供的镜像地址： https://php.cnpkg.org
> * LaravelChina社区提供的镜像地址： https://packagist.laravel-china.org
> * Composer中文网提供的镜像地址： https://packagist.phpcomposer.com

镜像使用方法:

全局配置（推荐）:
```
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```

取消镜像：
```
composer config -g --unset repos.packagist
```

在单独项目中使用：
```
composer config repo.packagist composer https://mirrors.aliyun.com/composer/
```

在单独项目中取消配置：
```
composer config --unset repos.packagist
```

---- 最后更新时间： 2020-03-25 10:02

