<?php

namespace App\Common\Middleware;

use App\Common\Route;
use EasySwoole\Http\Request;

class Middleware
{
    public $className = [
        'api' => '\App\Middleware\ApiMiddleware',
    ];

    public function handle(Request $request)
    {

        $classInfo = $this->getClassInfo($request->getUri()->getPath());
        if (!array_key_exists($classInfo['prefix'], $this->className)) {
            return true;
        }
        // php反射机制
        $reflectionClass = (new \ReflectionClass($this->className[$classInfo['prefix']]))->newInstanceArgs();
        return $reflectionClass->handle($request);
    }

    /**
     * 解析url path 信息，生成类名和方法名
     * @param $pathInfo
     * @return array
     */
    protected function getClassInfo($pathInfo)
    {
        $routePath = Route::getRoutePath($pathInfo);
        $routePath = explode('\\', $routePath);
        return ['prefix' => strtolower($routePath[3])];
    }
}