<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
return [
    'default_theme' => [// 在后台插件配置表单中的键名 ,会是config[text]
        'title' => '默认模板名', // 表单的label标题
        'type'  => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
        'value' => '',// 表单的默认值
        'tip'   => '默认用户访问网站时会使用此模板，留空表示不开启。' //表单的帮助提示
    ],
    'mobile_theme' => [// 在后台插件配置表单中的键名 ,会是config[text]
        'title' => '手机模板名', // 表单的label标题
        'type'  => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
        'value' => '',// 表单的默认值
        'tip'   => '在手机用户访问网站时会使用此模板，留空表示不开启。' //表单的帮助提示
    ],
];