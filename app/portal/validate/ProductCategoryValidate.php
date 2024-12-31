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

class ProductCategoryValidate extends Validate
{
    protected $rule = [
        'name' => 'require|unique:product_category',
        'list_order' => 'integer',
    ];
    protected $message = [
        'name.require' => '分类名称不能为空',
        'name.unique' => '分类已存在',
        'list_order.integer' => '排序只能是整数',
    ];

}