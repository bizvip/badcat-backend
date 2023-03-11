<?php

declare(strict_types=1);

ini_set('error_reporting', (string)E_ALL);

try {
// 定义应用目录
    define("APP_PATH", __DIR__.'/../application/');

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
} catch (\Throwable $e) {
    echo 'catch info ', PHP_EOL;
    echo $e->getMessage(), PHP_EOL, $e->getFile(), PHP_EOL, $e->getLine(), PHP_EOL, $e->getTraceAsString(), PHP_EOL;
    echo 'catch info end', PHP_EOL;
    exit(255);
}
