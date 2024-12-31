<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class SlideItemModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'slide_item';

    protected $type = [
        'more'        => 'array'
    ];

    /**
     * content 自动转化
     * @param $value
     * @return string
     */
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * content 自动转化
     * @param $value
     * @return string
     */
    public function setContentAttr($value)
    {
        return htmlspecialchars_decode($value, true);
    }
}
