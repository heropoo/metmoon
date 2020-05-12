---
title: 如何正确的使php进程以守护进程方式运行
date: 2020-05-12 10:33:50
layout: post
author: "Heropoo"
categories: 
    - PHP
tags:
    - PHP
    - 守护进程
excerpt: "如何正确的使php进程以守护进程方式运行"
---

守护进程是一个在后台运行并且不受任何终端控制的进程。 关于守护进程的详细解释可以看看[百度百科](https://baike.baidu.com/item/%E5%AE%88%E6%8A%A4%E8%BF%9B%E7%A8%8B)。

下面我们看看php的脚本程序怎么作为守护进程运行在后台。

在Linux中 , 大概有三种方式实现脚本后台化 :
- 1.在命令后添加一个`&`符号，比如 `php task.php &`。这个方法的缺点在于：如果终端关闭，无论是正常关闭还是非正常关闭，这个php进程都会随着终端关闭而关闭；其次是代码中如果有`echo`或者`var_dump()`之类的输出文本，会被输出到当前的终端窗口中。
- 2.使用`nohup`命令，比如`nohup php task.php &`。默认情况下，代码中`echo`或者`var_dump`之类输出的文本会被输出到php代码同级目录的`nohup.out`文件中。如果你关闭终端，该进程不会被关闭，依然会在后台持续运行。但是如果终端遇到异常退出或者终止，该php进程也会随即退出。本质上 也并非稳定可靠的守护进程方案 。
- 3.使用`fork`和`setsid`，这个方案在上面[百度百科](https://baike.baidu.com/item/%E5%AE%88%E6%8A%A4%E8%BF%9B%E7%A8%8B)中也有详细介绍，我们这里以php代码来演示：
```php
<?php
// deamon.php

// 一次fork  
$pid = pcntl_fork();
if ($pid < 0) {
    exit(' fork出错 ');
} else if($pid > 0) {
    exit(' 父进程退出 ');
}

// 将当前子进程提升会会话组组长 这是至关重要的一步 
if (!posix_setsid()) {
    exit(' setsid 出错 ');
}

// 二次fork
$pid = pcntl_fork();
if($pid < 0){
    exit(' fork 出错 ');
} else if($pid > 0) {
    exit(' 父进程退出 ');
}

// 真正的逻辑代码们 下面仅仅写个循环以示例
for($i = 1; $i <= 100; $i++){
    sleep(1);
    file_put_contents('daemon.log', $i, FILE_APPEND);
}
``` 

运行看看效果吧
```sh
php deamon.php
tail -f daemon.log
```

另外还有一种运行脚本的方式也值得一提：利用 `screen` / `tmux` 等软件，将脚本运行在可以在一个虚拟终端之上。

参考：
- https://github.com/elarity/advanced-php/blob/master/1.%20php%E8%BF%9B%E7%A8%8Bdaemon%E5%8C%96%E7%9A%84%E6%AD%A3%E7%A1%AE%E5%81%9A%E6%B3%95.md
- [Tmux 使用教程 阮一峰的网络日志](http://www.ruanyifeng.com/blog/2019/10/tmux.html)
- [SSH远程会话管理工具 - screen使用教程](https://www.vpser.net/manage/screen.html)

