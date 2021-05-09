<?php

namespace App\Common\Middleware;


abstract class BaseMiddleware
{
    /**
     * 权限组
     * @param $path
     */
    abstract public function middlewareGroup($path);
}