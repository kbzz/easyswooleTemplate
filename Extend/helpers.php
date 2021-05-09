<?php

if (!function_exists('config')) {
    /**
     * 获取和设置配置参数
     * @param string|array $name 参数名
     * @param mixed $value 参数值
     * @return mixed
     */
    function config($name = '', $value = null)
    {
        $config = \EasySwoole\EasySwoole\Config::getInstance();
        if (is_null($value) && is_string($name)) {
            return $config->getConf($name);
        } else {
            return $config->setConf($name, $value);
        }
    }
}

if (!function_exists('json')) {
    /**
     * json 数据处理
     * @param $data
     * @return false|mixed|string
     */
    function json($data)
    {
        if (is_array($data)) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            return json_decode($data, true);
        }
    }
}