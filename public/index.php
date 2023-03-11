<?php

// 定义应用目录
const APP_PATH = __DIR__.'/../application/';

// 判断是否安装
if (!is_file(APP_PATH.'admin/command/Install/install.lock')) {
    header("location:./install.php");
    exit;
}

// 加载框架引导文件
require __DIR__.'/../thinkphp/start.php';
