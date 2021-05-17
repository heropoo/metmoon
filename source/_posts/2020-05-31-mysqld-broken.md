---
title: 记录一次MySQL自动停机的问题处理
date: 2020-05-31 11:09:36
layout: post
author: "Heropoo"
categories: 
    - MySQL
tags:
    - MySQL
excerpt: "最近帮别人做的一个项目机器上面跑MySQL老是隔一段时间就自动停了，以下记录修复过程"
---

最近帮别人做的一个项目机器上面跑MySQL老是隔一段时间就自动停了。刚开始以为是以外停止，也没注意，就手动再启动。可是过了没两天又停止了。

后来仔细查了查`mysqld`的日志：
```log
2020-05-27T10:15:12.569342Z 0 [System] [MY-010116] [Server] /usr/libexec/mysqld (mysqld 8.0.17) starting as process 19493
2020-05-27T10:15:14.448256Z 0 [System] [MY-010229] [Server] Starting crash recovery...
2020-05-27T10:15:14.475411Z 0 [System] [MY-010232] [Server] Crash recovery finished.
2020-05-27T10:15:14.691345Z 0 [Warning] [MY-010068] [Server] CA certificate ca.pem is self signed.
2020-05-27T10:15:15.677386Z 0 [System] [MY-010931] [Server] /usr/libexec/mysqld: ready for connections. Version: '8.0.17'  socket: '/var/lib/mysql/mysql.sock'  port: 3306  Source distribution.
2020-05-27T10:15:15.951210Z 0 [System] [MY-011323] [Server] X Plugin ready for connections. Socket: '/var/lib/mysql/mysqlx.sock' bind-address: '::' port: 33060
2020-05-27T11:26:19.955004Z 0 [System] [MY-010116] [Server] /usr/libexec/mysqld (mysqld 8.0.17) starting as process 19757
2020-05-27T11:26:20.181302Z 0 [ERROR] [MY-012681] [InnoDB] mmap(137363456 bytes) failed; errno 12
2020-05-27T11:26:20.181360Z 1 [ERROR] [MY-012956] [InnoDB] Cannot allocate memory for the buffer pool
2020-05-27T11:26:20.181379Z 1 [ERROR] [MY-012930] [InnoDB] Plugin initialization aborted with error Generic error.
2020-05-27T11:26:20.181401Z 1 [ERROR] [MY-010334] [Server] Failed to initialize DD Storage Engine
2020-05-27T11:26:20.181543Z 0 [ERROR] [MY-010020] [Server] Data Dictionary initialization failed.
2020-05-27T11:26:20.183642Z 0 [ERROR] [MY-010119] [Server] Aborting
2020-05-27T11:26:20.184163Z 0 [System] [MY-010910] [Server] /usr/libexec/mysqld: Shutdown complete (mysqld 8.0.17)  Source distribution.
```

上面显示是`Cannot allocate memory for the buffer pool`，无法分配内存给缓存池。马上想到是内存不足，这台机器是1GB的内存，还跑着 `Nginx` 和 `PHP-FPM` 。

使用 `top` 看了看 `mysqld` 占用的内存达到了48% 。内存占用还是蛮高的。

当然了升级机器配置是比较好的办法，但是毕竟经费有限。所以我们先给它加个`swap`交换空间：
```sh
dd if=/dev/zero of=/swapfile bs=1M count=2048
mkswap /swapfile
swapon /swapfile
systemctl restart mysqld
```

上面我们加了2GB的交换空间给机器。然后重启 `mysqld`。再使用`top`看了看，发现交换空间渐渐被使用了。

睡了一觉起来一看，`mysqld`服务没有再自动停止，内存占用已经下降到`22%`，看了看错误日志，也是空的。

嗯，看来还不错嘛。再观察个几天看看，没问题的话应该就好了😎😎😎。