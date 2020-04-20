<?php

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
