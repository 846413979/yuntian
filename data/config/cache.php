<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('CACHE_DRIVER', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 更多的缓存连接
        'redis' => [
            // Redis缓存配置
            'type' => 'redis',
            'host' => '127.0.0.1', // Redis服务器地址
            'port' => 6379,       // Redis服务器端口
            'password' => '',     // Redis服务器密码（如果有的话）
            'select' => 0,        // Redis数据库索引
            'timeout' => 0,       // 连接超时时间（秒）
            'expire' => 3600,     // 缓存有效期（秒）
            'persistent' => false,// 是否使用长连接
            'prefix' => 'yuntian', // 缓存前缀
        ],
    ],
];