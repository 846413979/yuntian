<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:kane < chengjin005@163.com>
// +----------------------------------------------------------------------
namespace app\portal\model;

use think\Model;
use think\model\concern\SoftDelete;

class ProfessionModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'profession';

    public static $STATUS = array(
        0 => "禁用",
        1 => "启用",
    );

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';

    // 软删除
    use SoftDelete;
    protected $deleteTime = 'delete_time';
}
