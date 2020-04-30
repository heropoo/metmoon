<?php

/**
 * @see https://github.com/elarity/advanced-php/blob/master/1.%20php%E8%BF%9B%E7%A8%8Bdaemon%E5%8C%96%E7%9A%84%E6%AD%A3%E7%A1%AE%E5%81%9A%E6%B3%95.md
 */

// 一次fork  
$pid = pcntl_fork();
if ( $pid < 0 ) {
  exit( ' fork error. ' );
} else if( $pid > 0 ) {
  exit( ' parent process. ' );
}
// 将当前子进程提升会会话组组长 这是至关重要的一步 
if ( ! posix_setsid() ) {
  exit( ' setsid error. ' );
}
// 二次fork
$pid = pcntl_fork();
if( $pid < 0 ){
  exit( ' fork error. ' );
} else if( $pid > 0 ) {
  exit( ' parent process. ' );
}

// 真正的逻辑代码们 下面仅仅写个循环以示例
for( $i = 1 ; $i <= 100 ; $i++ ){
  sleep( 1 );
  file_put_contents( 'daemon.log', $i, FILE_APPEND );
}