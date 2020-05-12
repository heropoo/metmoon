<?php

umask(0); //把文件掩码清0 

// 一次fork  
$pid = pcntl_fork();
if ($pid < 0) {
    exit(' fork出错 ');
} else if ($pid > 0) {
    exit(' 父进程退出 '); // 父进程退出,子进程变成孤儿进程被1号进程收养，进程脱离终端
}

// 将当前子进程提升会会话组组长 这是至关重要的一步 
if (!posix_setsid()) {
    exit(' setsid 出错 ');
}

//修改当前进程的工作目录，由于子进程会继承父进程的工作目录，修改工作目录以释放对父进程工作目录的占用。
chdir('/tmp');

// 二次fork
// 通过上一步，我们创建了一个新的会话组长，进程组长，且脱离了终端，但是会话组长可以申请重新打开一个终端，为了避免
// 这种情况，我们再次创建一个子进程，并退出当前进程，这样运行的进程就不再是会话组长。
$pid = pcntl_fork();
if ($pid < 0) {
    exit(' fork 出错 ');
} else if ($pid > 0) {
    exit(' 父进程退出 ');
}

//由于守护进程用不到标准输入输出，关闭标准输入，输出，错误输出描述符
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);

// 真正的逻辑代码们 下面仅仅写个循环以示例
for ($i = 1; $i <= 100; $i++) {
    sleep(1);
    file_put_contents('daemon.log', $i, FILE_APPEND);
}
