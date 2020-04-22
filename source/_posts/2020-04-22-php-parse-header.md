---
layout: post
title: PHP解析header头部信息
date: 2020-04-22 14:36:43
author: "Heropoo"
categories: 
    - PHP
tags:
    - PHP
    - HTTP
excerpt: "现在写接口经常会使用header来传递一些验证信息，我们用各种php框架可以轻松的获取到，但是它底层是怎么做的呢？我们今天来聊一聊。"
---

现在写接口经常会使用header来传递一些验证信息，我们用各种php框架可以轻松的获取到，但是它底层是怎么做的呢？我们今天来聊一聊。

我们知道，在php中获取`get`参数（Query String Parameters）可以使用`$_GET`全局变量，获取`post`参数使用`$_POST`全局变量。但是我们想要获取`header`，好像缺没有一个类似`$_HEADER`的全局变量来供我们使用。那我们想要获取到`header`信息该怎么办呢？也不卖关子了，我们可以从`$_SERVER`这个全局变量中获取到。接下来我们来进行这个操作：

我们写一个简单的php文件测试一下：
```php
<?php
// header.php

var_dump($_SERVER);
?>
```

在这里我的访问地址是： http://localhost/header.php

我们使用`postman`工具或者`curl`命令请求下这个地址，并加入我们自定义的一个头 `token: 123456`
```sh
curl -H "token: 123456" http://localhost/header.php
```

在输出中我们可以找到一项
```
// ... 省略了好多

["HTTP_TOKEN"]=>
string(6) "123456"

// ... 省略了好多
```

而这个`HTTP_TOKEN`就是我们想要的东西，我们传入的小写`token`， 在这里也转换成了大写。

我们再试一个，这次我们传递两个`header`：
```sh
curl -H "test_token: 654321" -H "token: 123456" http://localhost/header.php
```

测试发现，我们在输出中只找到了`HTTP_TOKEN`，缺没有找到我们预想的`HTTP_TEST_TOKEN`。不要着急，我们把下划线`_`换成连字符`-`试试：
```
curl -H "test-token: 654321" -H "token: 123456" http://localhost/header.php
```

看看输出，这下两个`header`都找到了。
```
// ... 省略了好多

["HTTP_TEST_TOKEN"]=>
string(6) "654321"
["HTTP_TOKEN"]=>
string(6) "123456"

// ... 省略了好多
```

综上所述：传递`header`的键值，只能是单个词或者以中划线`-`连接的词。不然php会忽略。

好了接下来我们在实际应用中，只要把`$_SERVER`中的以`HTTP_`开头的下标都提取出来然后转换成首字母大写的格式就好了。

```php
<?php
function parse_headers(){
    $headers = [];
    foreach ($_SERVER as $name => $value)
    {
        if (substr($name, 0, 5) == 'HTTP_')
        {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

$headers = parse_headers();
var_dump($headers);

?>
```

看看输出：
```
array(5) {
  ["Host"]=>
  string(9) "localhost"
  ["User-Agent"]=>
  string(11) "curl/7.65.3"
  ["Accept"]=>
  string(3) "*/*"
  ["X-Token"]=>
  string(6) "654321"
  ["Token"]=>
  string(6) "123456"
}
```

好了，有模有样，完美！✌🤓😎

