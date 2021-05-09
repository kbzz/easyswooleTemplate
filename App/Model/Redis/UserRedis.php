<?php

namespace App\Model\Redis;

use App\Service\ExpireTimeService;

use EasySwoole\Component\Singleton;

class UserRedis extends Redis
{
    use Singleton;

    public function user($key, $param = [])
    {
        if (empty($param)) {
            $res = $this->redis->hgetall($key);
        } else {
            $res = $this->redis->hMset($key, [
                'id' => $param['id'],
                'mobile' => $param['mobile'],
                'nickname' => $param['nickname'],
                'headimg' => $param['headimg'],
                'mini_openid' => $param['mini_openid'],
            ]);
            $this->redis->Expire($key, ExpireTimeService::userTime());
        }
        return $res;
    }

    public function tempUser($key, $param = [])
    {
        if (empty($param)) {
            $res = $this->redis->hgetall($key);
        } else {
            $res = $this->redis->hmset($key, [
                'openId' => $param['openId'],
                'nickName' => $param['nickName'],
                'gender' => $param['gender'],
                'language' => $param['language'],
                'city' => $param['city'],
                'province' => $param['province'],
                'country' => $param['country'],
                'avatarUrl' => $param['avatarUrl'],
                'unionId' => $param['unionId'] ?? '',
            ]);
            $this->redis->Expire($key, ExpireTimeService::userTime());
        }
        return $res;
    }
}