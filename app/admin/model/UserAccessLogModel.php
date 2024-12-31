<?php

namespace app\admin\model;

use app\portal\model\ProductModel;
use app\portal\service\RedisService;
use think\Model;
use think\model\concern\SoftDelete;

class UserAccessLogModel extends Model
{
    protected $name = 'user_access_log';
    protected $autoWriteTimestamp = true;

    protected function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function saveLog($ip,$message_id)
    {
        $RedisService = new RedisService();
        $list = $RedisService->getUrl($ip);
        $data = [];
        foreach ($list['url_list'] as $k=>$v){
            $data[] = [
                'ip' => $ip,
                'message_id' => $message_id,
                'uri' => $v,
                'params' => $list['params_list'][$k],
            ];
        }
        try {
            $this->saveAll($data);
            // 删除redis
            $RedisService->delUrl($ip);
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

}