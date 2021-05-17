---
title: php冷知识 - 输出控制函数
date: 2020-04-29 14:11:52
layout: post
author: "Heropoo"
categories: 
    - PHP
tags:
    - 输出控制
    - PHP
excerpt: "大家在工作中应该也用过`ob_start()`、`ob_clean()`、`ob_get_contents()`等这类的输出控制函数，我们今天就来详细聊聊它们。"
---
大家在工作中应该也用过`ob_start()`、`ob_clean()`、`ob_get_contents()`等这类的输出控制函数，我们今天就来详细聊聊它们。

## 常用的函数有这些：
- ob_start — 打开输出控制缓冲
- ob_clean — 清空（擦掉）输出缓冲区
- ob_end_clean — 清空（擦除）缓冲区并关闭输出缓冲
- ob_get_clean — 得到当前缓冲区的内容并删除当前输出缓。
- ob_get_contents — 返回输出缓冲区的内容
- ob_end_flush — 冲刷出（送出）输出缓冲区内容并关闭缓冲
- ob_flush — 冲刷出（送出）输出缓冲区中的内容
- ob_get_flush — 刷出（送出）缓冲区内容，以字符串形式返回内容，并关闭输出缓冲区。
- flush — 刷新输出缓冲
- ob_get_length — 返回输出缓冲区内容的长度
- ob_get_level — 返回输出缓冲机制的嵌套级别
- ob_get_status — 得到所有输出缓冲区的状态

## 接下来我们举例说明：
```php
<?php

//默认的缓冲级别是0，ob_start()之后加一
if (ob_get_level() == 0) ob_start();

for ($i = 0; $i<10; $i++){

    echo "<br> Line to show.";

    ob_flush();
    flush();
    sleep(2);
}

echo "Done.";

ob_end_flush();
```
浏览器运行它，发现每两秒输出一行。


(未完待续)