<?php

declare(strict_types=1);

// 定义应用目录
const APP_PATH = __DIR__.'/../application/';

// 判断是否安装
if (!is_file(APP_PATH.'admin/command/Install/install.lock')) {
    header("location:./install.php");
    exit;
}

// 加载框架引导文件
require __DIR__.'/../thinkphp/base.php';

// 绑定到admin模块
\think\Route::bind('admin');

// 关闭路由
\think\App::route(false);

// 设置根url
\think\Url::root('');

// 执行应用
\think\App::run()->send();
