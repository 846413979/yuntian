<?php

namespace app\portal\service;

use think\cache\driver\Redis;
use think\facade\Cache;

class RedisService
{
    public static function setUrl($ip,$url,$params){
        $redisCfg = Cache::getConfig('stores.redis');
        $redis = new Redis($redisCfg);

        if ($redis->lLen($ip) == 0 || $redis->lRange($ip, -1, -1)[0] != $url) {
            $redis->rPush($ip, $url);
            $redis->rPush($ip.'-params', json_encode($params));
            $redis->expire($ip,$redisCfg['expire']);
            $redis->expire($ip.'-params',$redisCfg['expire']);
        }
    }

    public static function getUrl($ip){
        $redisCfg = Cache::getConfig('stores.redis');
        $redis = new Redis($redisCfg);
        $url_list = $redis->lRange($ip, 0, -1);
        $params_list = $redis->lRange($ip.'-params', 0, -1);
        return ['url_list'=>$url_list,'params_list'=>$params_list];
    }

    public static function delUrl($ip){
        $redisCfg = Cache::getConfig('stores.redis');
        $redis = new Redis($redisCfg);
        $redis->del($ip);
        $redis->del($ip.'-params');
    }

}