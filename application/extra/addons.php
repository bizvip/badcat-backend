<?php

return [
    'autoload' => false,
    'hooks' => [
        'leesignhook' => [
            'leesign',
        ],
        'admin_login_init' => [
            'loginbg',
        ],
        'response_send' => [
            'loginbgindex',
        ],
        'index_login_init' => [
            'loginbgindex',
        ],
        'config_init' => [
            'nkeditor',
        ],
        'app_init' => [
            'qrcode',
        ],
    ],
    'route' => [
        '/example$' => 'example/index/index',
        '/example/d/[:name]' => 'example/demo/index',
        '/example/d1/[:name]' => 'example/demo/demo1',
        '/example/d2/[:name]' => 'example/demo/demo2',
        '/leesign$' => 'leesign/index/index',
        '/qrcode$' => 'qrcode/index/index',
        '/qrcode/build$' => 'qrcode/index/build',
    ],
    'priority' => [],
    'domain' => '',
];
