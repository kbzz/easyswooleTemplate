<?php

namespace App\Middleware;

use App\Common\Middleware\BaseMiddleware;
use App\Model\Redis\UserRedis;
use EasySwoole\Http\Request;

class ApiMiddleware extends BaseMiddleware
{
    /**
     * 中间件入口
     * @param Request $request
     * @return bool
     */
    public function handle(Request $request)
    {
        $path = $request->getUri()->getPath();

        $middlewareGroup = $this->middlewareGroup($path);
        foreach ($middlewareGroup as $middleware) {
            $result = $this->$middleware($request);
            // 如果不是true的话，就直接返回结果
            if ($result !== true) {
                return $result;
            }
        }
        return true;
    }

    /**
     * 中间件过程
     * apiBase, apiMiniLogin
     * @param $path
     * @return array
     */
    public function middlewareGroup($path)
    {
        $middlewareGroup[] = 'apiMiniBase';
        switch ($path) {
            case '/api/v1/login/miniAuth':
                $middlewareGroup[] = 'miniLogin';
                $middlewareGroup[] = 'waitLogin';
                break;

        }
        return $middlewareGroup;
    }


    private function miniLogin(Request $request)
    {
        return true;
    }


    private function waitLogin(Request $request)
    {
        return true;
    }


    /**
     * 基础中间件
     * @param Request $request
     * @return string|array|bool
     */
    private function apiMiniBase(Request $request)
    {
        return true;
    }
}