---
layout: post
title: php冷知识 - 从命令行参数列表中获取选项
date: 2020-04-20 10:34:14
author: "Heropoo"
categories: 
    - PHP
tags:
    - PHP
    - 命令行
excerpt: "分享一个php的冷知识 - 从命令行参数列表中获取选项"
---

分享一个php的冷知识 - ，从命令行参数列表中获取选项

用到的函数是`getopt`

## 说明
函数签名是这样的
```php
getopt ( string $options [, array $longopts [, int &$optind ]] ) : array|bool false
```
解析传入脚本的选项，成功返回数组，解析失败返回false

## 参数
- `options` 该字符串中的每个字符会被当做选项字符，匹配传入脚本的选项以单个连字符(-)开头。 比如，一个选项字符串 "x" 识别了一个选项 -x。 只允许 a-z、A-Z 和 0-9。

- `longopts` 选项数组。此数组中的每个元素会被作为选项字符串，匹配了以两个连字符(--)传入到脚本的选项。 例如，长选项元素 "opt" 识别了一个选项 --opt。

- `optind` 如果存在`optind`参数，则参数解析停止的索引将被写入此变量。

###  参数值
`options` 可能包含了以下元素：
- 单独的字符（不接受值）
- 后面跟随冒号的字符（此选项需要值）
- 后面跟随两个冒号的字符（此选项的值可选）

`options` 和 `longopts` 的格式几乎是一样的，唯一的不同之处是 `longopts` 需要是选项的数组（每个元素为一个选项），而 `options` 需要一个字符串（每个字符是个选项）。

## 例子
说了这么多，我们举例说明吧
### 1. 基本用法
```php
<?php
// Script 1.php
$options = getopt("h:p:d");
var_dump($options);
?>
```

命令行下测试看看
```sh
php 1.php -h 127.0.0.1 -p 8000 -d
```

以上例程会输出：
```
array(3) {
  ["h"]=>
  string(9) "127.0.0.1"
  ["p"]=>
  string(4) "8000"
  ["d"]=>
  bool(false)
}
```

选项参数也可以中间不要空格
```sh
php 1.php -h 127.0.0.1 -p 8000 -d
```

php5.3之后还可以使用 "=" 作为 参数和值的分隔符
```sh
php 1.php -h=127.0.0.1 -p=8000 -d
```

### 2. 引入长选项
```php
<?php
// Script 2.php
$shortopts  = "";       // 短选项 用字母字符串
$shortopts .= "h:";     // 必选选项 字母后面一个冒号
$shortopts .= "p::";    // 可选选项 字母后面两个冒号
$shortopts .= "vd";     // 无需值的选项 字母后面没有冒号

$longopts  = array(     // 长选项 用单词的数组
    "host:",            // 必选选项 单词后面一个冒号
    "port::",           // 可选选项 单词后面两个冒号
    "version",          // 无需值的选项 单词后面没有冒号
    "debug",            // 无需值的选项 单词后面没有冒号
);

$options = getopt($shortopts, $longopts);
var_dump($options);
?>
```

运行测试：
```sh
php 2.php -h127.0.0.1 -p8000 -d --host=127.0.0.1 --port=8000 --version --debug
```

输出：
```
array(7) {
  ["h"]=>
  string(9) "127.0.0.1"
  ["p"]=>
  string(4) "8000"
  ["d"]=>
  bool(false)
  ["host"]=>
  string(9) "127.0.0.1"
  ["port"]=>
  string(4) "8000"
  ["version"]=>
  bool(false)
  ["debug"]=>
  bool(false)
}
```

### 3. 同一选项可以传递多次
运行测试：
```sh
php 2.php -d -d --version --debug --port=123 --port=234
```

输出：
```
array(4) {
  ["d"]=>
  array(2) {     // 多个选项参数值以数组呈现
    [0]=>
    bool(false)
    [1]=>
    bool(false)
  }
  ["version"]=>
  bool(false)
  ["debug"]=>
  bool(false)
  ["port"]=>
  array(2) {
    [0]=>
    string(3) "123"
    [1]=>
    string(3) "234"
  }
}
```

### 4. 使用 optind
```php
// Script 3.php
$optind = null;
$opts = getopt('a:b:', [], $optind);
var_dump($argv);
var_dump($optind);
$pos_args = array_slice($argv, $optind);  // 从数组中取出一段
var_dump($pos_args);
```

运行测试：
```sh
php 3.php -a 1 -b 2 -- test
```

输出：
```
array(7) {
  [0]=>
  string(7) "cli.php"
  [1]=>
  string(2) "-a"
  [2]=>
  string(1) "1"
  [3]=>
  string(2) "-b"
  [4]=>
  string(1) "2"
  [5]=>
  string(2) "--"
  [6]=>
  string(4) "test"
}
int(6)      // $optind = 6
array(1) {
  [0]=>
  string(4) "test"
}
```

至此以后就可以写出漂亮优雅的cli程序了😎😎😎


## 参考
- https://www.php.net/manual/zh/function.getopt.php