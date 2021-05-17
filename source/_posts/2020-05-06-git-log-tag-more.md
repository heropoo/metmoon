---
title: 怎么查看git仓库当前的分支、最后一次commitId、tag等
date: 2020-05-06 18:34:57
layout: post
author: "Heropoo"
categories: 
    - Git
tags:
    - Git
excerpt: "最近想把项目的git仓库版本作为项目版本来使用，就研究了下，做点笔记"
---

最近想把项目的git仓库版本作为项目版本来使用，就研究了下，做点笔记。

## 查看当前分支名称
```sh
git symbolic-ref --short -q HEAD
# 输出 master
```

## 查看当前最后一次提交的commit_id
```sh
git log -1 --pretty=format:%H # 完整的
# 输出 7b6b2803d2b7135b239d062847816e55a810371e
git log -1 --pretty=format:%h # 前7位
# 输出 7b6b280
```

## 查看最后一次提交的时间
```sh
git log -1 --format="%ct"
输出 1588759297
```

这里输出是unix时间戳，需要自己转换下，如果在shell中可以这么写
```sh
#!/bin/sh

commit_ts=`git log -1 --format="%ct"`
sys=`uname`
if [ $sys = "Darwin" ] 
then
  commit_time=`date -r${commit_ts} +"%Y-%m-%d %H:%M:%S"`
else
  commit_time=`date -d @${commit_ts} +"%Y-%m-%d %H:%M:%S"`
fi
echo $commit_time
```

MacOS和Linux有差别，做个系统判断

## 查看最后一次提交对应的tag
```sh
git log -1 --decorate=short --oneline|grep -Eo 'tag: (.*)[,)]+'|awk '{print $2}'|sed 's/)//g'|sed 's/,//g'
```

这里使用`git log -1 --decorate=short --oneline`，输出
```sh
e4df105 (HEAD -> develop, tag: v0.1.1, origin/develop) 测试提交
```

然后使用grep正则表达式配合awk、sed提取出了`v0.1.1`

好了，就这些吧～
