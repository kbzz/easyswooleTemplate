<?php

namespace App;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class ExceptionHandler
{
    public static function handle(\Throwable $exception, Request $request, Response $response)
    {
        // 当前在全局Exception，当有onException的异常报错，就不会到全局里面来
        $code = 200;
        $response['status'] = '0';
        $response['msg'] = '网络错误';

        $response->write(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->withStatus($code);
    }
}