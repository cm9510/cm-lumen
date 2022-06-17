<?php

return [

    // System configuration item
    'name' => env('APP_NAME', 'Lumen'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'PRC',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'vendor_dir'=> env('VENDOR_DIR'),

    // application
    'redis'=> [
        'host'=> env('REDIS_HOST', '127.0.0.1'),
        'port'=> env('REDIS_PORT', 6379),
        'timeout'=> env('REDIS_TIMEOUT', 30),
        'password'=> env('REDIS_PASSWORD', '')
    ],

    // OSS
    'oss'=> [
        'access_key'=> env('OSS_ACCESS_KEY',''),
        'access_secret'=> env('OSS_ACCESS_SECRET',''),
        'endpoint'=> env('OSS_ENDPOINT',''),
        'bucket'=> env('OSS_BUCKET',''),
        'domain'=> env('OSS_DOMAIN',''),
        'cover_style'=> env('OSS_COVER_STYLE',''),
    ],

    // wechat
    'wx_applet'=> [
        'appid'=> env('WX_APPID',''),
        'secret'=> env('WX_SECRET',''),
        'msg_detail_page'=> env('APPLET_MSG_PAGE','')
    ]

];
