<?php

namespace App\Common\Validate;

use App\Common\Route;
use EasySwoole\Http\Request;

class Validate
{
    public function handle(Request $request, $validateData = [])
    {
        // 验证的参数
        if (empty($validateData)) {
            $validateData = $request->getRequestParam();
        }

        $classInfo = $this->getClassInfo($request->getUri()->getPath());

        // 判断类是否存在
        if (!class_exists($classInfo['className'])) {
            return true;
        }
        // php反射机制
        $reflectionClass = (new \ReflectionClass($classInfo['className']))->newInstanceArgs();
        return $reflectionClass->validate($validateData, $classInfo['actionName']);
    }

    /**
     * 解析url path 信息，生成类名和方法名
     * @param $pathInfo
     * @return array
     */
    protected function getClassInfo($pathInfo)
    {
        $routePath = Route::getRoutePath($pathInfo);
        $routePath = explode('@', $routePath);
        $className = str_replace('Controller', 'Validate', $routePath[0]) . 'Validate';
        $actionName = $routePath[1];
        return ['className' => $className, 'actionName' => $actionName];
    }
}