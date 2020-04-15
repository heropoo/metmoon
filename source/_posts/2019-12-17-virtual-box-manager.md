---
layout: post
title:  "使用命令操作VirtualBox"
date:   2019-12-17 17:55:44
author: "Heropoo"
categories: 
    - Linux
tags:
    - Linux
    - 虚拟化
excerpt: "最近一时头脑发热找了本汇编的书在啃，书中是使用手动操作VirtualBox挂载硬盘调试的不太方便，遂使用命令搞起"
---
最近一时头脑发热找了本汇编的书在啃，书中是使用手动操作`VirtualBox`挂载硬盘调试的不太方便，遂使用命令搞起，效果还不错。

VirtualBox是个免费的虚拟机软件，不论linux、win、mac下都可以直接安装使用。同时它还支持使用命令控制，感觉这一点给了开发者无限的想象空间和创造力。

而所有的操作都是一个`VBoxManage`命令完成的。现在整理一下：

下面我们以创建一个名为`learnAsm`的虚拟机为例，展示下基本的操作。

## 创建虚拟机
创建并注册
```sh
VBoxManage createvm --name learnAsm --register 
```

## 删除虚拟机
（！！！会删除所有虚拟硬盘，谨慎操作！！！）
```sh
VBoxManage unregistervm --delete learnAsm
```

## 注册虚拟机 
假如你注销了，或者从别人那里复制来的虚拟机文件，可以重新注册它
```sh
VBoxManage registervm <your vms path>/learnAsm.vbox
```

## 仅注销虚拟机 
注销之后VirtualBox列表中显示了
```sh
VBoxManage unregistervm learnAsm
```

## 列出已有的虚拟机
```sh
VBoxManage list vms
```

## 设置系统类型Ubuntu_64
```sh
VBoxManage modifyvm learnAsm --ostype Ubuntu_64
```

## 设置内存大小1G
```sh
VBoxManage modifyvm learnAsm --memory 1024 #单位MB
```

## 建立虚拟磁盘
```sh
VBoxManage createmedium --filename HDD10G.vdi --size 10000 #单位MB
```

## 创建存储控制器IDE、SATA
```sh
VBoxManage storagectl learnAsm --name IDE --add ide --controller PIIX4 --bootable on
VBoxManage storagectl learnAsm --name SATA --add sata --controller IntelAhci --bootable on

# 移除
VBoxManage storagectl learnAsm --name IDE --remove
VBoxManage storagectl learnAsm --name SATA --remove
```

## 关联虚拟机磁盘
```sh
VBoxManage storageattach learnAsm --storagectl SATA --port 0 --device 0 --type hdd --medium HDD10G.vdi
VBoxManage storageattach learnAsm --storagectl IDE --port 0 --device 0 --type hdd --medium HDD10G.vdi

# 解除关联
VBoxManage storageattach learnAsm --storagectl SATA --port 0 --device 0 --type hdd --medium none
VBoxManage storageattach learnAsm --storagectl IDE --port 0 --device 0 --type hdd --medium none
```

## 增加光驱
关联光盘镜像文件
```sh
VBoxManage storageattach learnAsm --storagectl IDE --port 1 --device 0 --type dvddrive --medium ubuntu.iso

# 解除关联：
VBoxManage storageattach learnAsm --storagectl IDE --port 1 --device 0 --type dvddrive --medium none
```

## 设置CPU数量（必须打开IOAPIC）
```sh
VBoxManage modifyvm learnAsm  --ioapic on
VBoxManage modifyvm learnAsm --cpus 4
```

## 设置CPU运行峰值
```sh
VBoxManage modifyvm learnAsm --cpuexecutioncap 80
```

## 查看虚拟机信息
```sh
VBoxManage -v
VBoxManage list vms  #列出虚拟机
VBoxManage list runningvms  #列出正在运行的虚拟机
VBoxManage showvminfo learnAsm #显示虚拟机learnAsm的信息
VBoxManage list hdds #列出硬盘
VBoxManage list dvds #列出dvd
```

## 启动虚拟机
```sh
VBoxManage startvm learnAsm --type headless #--type headless参数是无窗口静默启动
```

## 保持状态关闭虚拟机
```sh
VBoxManage controlvm learnAsm savestate
```

## 放弃已保存的状态
```sh
VBoxManage discardstate learnAsm
```

## 断电关闭虚拟机
```sh
VBoxManage controlvm learnAsm poweroff
```

## 正常关机[不能彻底关闭，一直处于stopping状态]
```sh
VBoxManage controlvm learnAsm acpipowerbutton
```

常用的大概就这些吧。

我是配合Makefile使用的，这里附上，大家感兴趣可以看看
```Makefile
ASM_FILE=c05_mbr.asm
DIST_PATH=.
VHD_FILE=$(DIST_PATH)/boot.vhd
VM_NAME=learn-asm

$(VHD_FILE): mbr.bin
	-rm $(VHD_FILE)
	VBoxManage convertfromraw mbr.bin $(VHD_FILE) --format VHD --variant Fixed

mbr.bin: $(ASM_FILE)
	nasm -f bin $(ASM_FILE) -o mbr.bin -l mbr.list

run: $(VHD_FILE) change-vm-vhd
	VBoxManage startvm $(VM_NAME)

stop:
	-VBoxManage controlvm $(VM_NAME) savestate
	-VBoxManage discardstate $(VM_NAME)

create-vm: $(VHD_FILE)
	VBoxManage createvm --name $(VM_NAME) --register
	VBoxManage modifyvm $(VM_NAME) --memory 64
	VBoxManage storagectl $(VM_NAME) --name SATA --add sata --controller IntelAhci --bootable on

change-vm-vhd: $(VHD_FILE)
	cp $(VHD_FILE) $(VHD_FILE).bk
	-VBoxManage discardstate $(VM_NAME)
	-VBoxManage storageattach $(VM_NAME) --storagectl SATA --port 0 --device 0 --type hdd --medium none
	-VBoxManage closemedium disk $(VHD_FILE) --delete # 因为硬盘是重新删除创建的，uuid也变了，直接挂载无法启动。所以这里先删除，再挂载。
	mv $(VHD_FILE).bk $(VHD_FILE)
	VBoxManage storageattach $(VM_NAME) --storagectl SATA --port 0 --device 0 --type hdd --medium $(VHD_FILE)

unregister-vm:
	-VBoxManage unregistervm $(VM_NAME)

delete-vm:
	-VBoxManage unregistervm --delete $(VM_NAME)

clean :
	-rm -rf *.vhd *.bin *.list
```


参考：
 - https://www.jianshu.com/p/a2d4840341fb
 - https://www.virtualbox.org/manual/