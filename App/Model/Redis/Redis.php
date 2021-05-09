<?php


namespace App\Model\Redis;

use EasySwoole\Component\Singleton;

class Redis
{
    use Singleton;
    public $redis;

    public function __construct()
    {
        $this->redis = \EasySwoole\Pool\Manager::getInstance()->get('redis')->getObj();

    }

    /**
     * hash 二维数组
     * @param $key
     * @param array $param
     * @return mixed
     */
    public function hMsetArray($key, $param = [])
    {
        if (empty($param)) {
            $res = $this->redis->hgetall($key);
            if (!empty($res)) {
                foreach ($res as &$value) {
                    $value = unserialize($value);
                }
            }
        } else {
            foreach ($param as &$value) {
                $value = serialize($value);
            }
            $res = $this->redis->hMset($key, $param);
        }
        return $res;
    }


    /**
     * hash 二维数组
     * @param $key
     * @param array $param
     * @return mixed
     */
    public function hgetOne($key, $hashKey)
    {
        $res = $this->redis->hget($key, $hashKey);
        if (!empty($res)) {
            $res = unserialize($res);
        }
        return $res;
    }

    public function priceTotal($key, $price)
    {
        $num = $this->redis->get($key) ?? 0;
        $this->redis->set($key, numberFormat($num + $price));
    }


    public function __call($name, $value)
    {
        return $this->redis->$name(...$value);
    }

}