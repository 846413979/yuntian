<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\portal\validate;

use app\admin\model\RouteModel;
use think\Validate;

class ProductValidate extends Validate
{
    protected $rule = [
        'category_id' => 'require',
        'title' => 'require',
        'list_order' => 'integer',
        'thumbnail' => 'require'
    ];
    protected $message = [
        'category_id.require' => '请指定分类',
        'name.require' => '标题不能为空',
        'list_order.integer' => '排序不能为空',
        'thumbnail.require' => '缩略图不能为空'
    ];

}