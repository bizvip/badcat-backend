<?php

use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

// gateway 进程，这里使用Text协议，可以用telnet测试
$gateway = new Gateway("websocket://0.0.0.0:8288");
// gateway名称，status方便查看
$gateway->name = 'YourAppGateway';
// gateway进程数
$gateway->count = 8;
// 本机ip，分布式部署时使用内网ip
$gateway->lanIp = '127.0.0.1';
// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
// 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
$gateway->startPort = 3900;
// 服务注册地址
$gateway->registerAddress = '127.0.0.1:1238';

// 心跳检测
$gateway->pingInterval = 23;

$gateway->pingNotResponseLimit = 0;

// 服务端定时向客户端发送的数据
$gateway->pingData = '{"type":"ping"}';

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}

