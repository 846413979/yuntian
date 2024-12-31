<?php

namespace app\admin\model;

use app\portal\model\ProductModel;
use think\Model;
use think\model\concern\SoftDelete;

class MessageModel extends Model
{
    protected $name = 'message';

    protected $type = [
        'lifting_capacity' => 'array',
        'job_level' => 'array',
        'sling_available' => 'array',
    ];

    protected $autoWriteTimestamp = true;

    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function adminMessageList($where = array())
    {
        return $this->where($where)->order('create_time desc')->paginate(10);
    }

    //已删除列表
    public function deletedMessageList($where = array())
    {
        return $this->onlyTrashed()->where($where)->order('delete_time desc')->paginate(10);
    }


    public function saveMessage($ip, $data)
    {
        if (!empty($data['product_id'])) {
            $productModel = new ProductModel();
            $product = $productModel->find($data['product_id']);
            $data['product_name'] = $product['title'];
            $data['product_image'] = $product['thumbnail'];

            if (!empty($data['lifting_capacity'])) {
                $lifting_capacity = $data['lifting_capacity'];
                foreach ($lifting_capacity as $key => $value) {
                    $data['lifting_capacity'][$key] = $product['lifting_capacity'][$value];
                }
            }

            if (!empty($data['job_level'])) {
                $job_level = $data['job_level'];
                foreach ($job_level as $key => $value) {
                    $data['job_level'][$key] = $product['job_level'][$value];
                }
            }

            if (!empty($data['sling_available'])) {
                $sling_available = $data['sling_available'];
                foreach ($sling_available as $key => $value) {
                    $data['sling_available'][$key] = $product['sling_available'][$value];
                }
            }

        }
        $data['ip'] = $ip;
        return $this->save($data);

    }

}