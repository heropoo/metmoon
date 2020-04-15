---
layout: post
title:  "聊一聊php的序列化"
date:   2020-03-31 22:41:55
author: "Heropoo"
categories: 
    - PHP
tags:
    - PHP
    - 序列化
excerpt: "最近在项目种经常用redis缓存序列化的数据，有点心得，我们现在聊一聊php的序列化操作"
---
最近在项目种经常用`redis`缓存数据序列化的数据，有点心得，我们现在聊一聊php的序列化操作

我们经常使用的是一对学列化函数`serialize`和`unserialize`。常用的操作很简单，就是：

1. 使用`serialize`序列化我们的变量使其变成字符串。
```php
$data = ['id'=>1, 'name'=>'xiaoming', 'sex'=>1];
$dataString = serialize($data);
// echo $dataString
```

2. 这些字符串可以离线保存到文件、数据库或者一些缓存介质中。
```php
// example
$redis->set('test_user1', $dataString);
```

3. 在使用的时候从上面的一些介质中取出字符串，然后调用`unserialize`反序列化到变量。
```php
$dataString = $redis->get('test_user1');
$data = unserialize($dataString);
```

我们再看看php各种数据类型序列化之后的结果如何：
1. 整型int
```php
echo serialize(1);
// 输出 i:1;
```

2. 浮点型float
```php
echo serialize(1.1);
// 输出 d:1.1;
```

3. 布尔bool
```php
echo serialize(true);
// 输出 b:1;
echo serialize(false);
// 输出 b:0;
```

4. 空值null
```php
echo serialize(null);
// 输出 n:1;
```

5. 字符串string
```php
echo serialize('1');
// 输出 s:1:"1";
```

6. 数组array
```php
echo serialize([]); //空数组
// 输出 a:0:{}
echo serialize([1]);
// 输出 a:1:{i:0;i:1;}
```

7. 对象object

```php
class A{}
$a = new A();
echo serialize($a);
// 输出 O:1:"A":0:{}

class B{
	public $name;
}
$b = new B();
echo serialize($b);
// 输出 O:1:"B":1:{s:4:"name";N;}

$c = new stdClass();
echo serialize($b);
// 输出 O:8:"stdClass":0:{}

$d = function(){
	return 1;
};
echo serialize($d);
// 输出报错 PHP Warning:  Uncaught Exception: Serialization of 'Closure' is not allowed
```

8. 资源resource

```php
$f = fopen("/tmp/t.txt", "r");  //假如文件/tmp/t.txt存在
echo serialize($f);
fclose($f);
// 输出 todo
```

从上面可以看来php的serialize函数无法序列化一个匿名函数

（未完待续）