<?php

namespace App\Common;


class Route
{
    /**
     * 路由格式
     *  api 格式： api/v1/控制器/方法名
     *  admin 格式： admin/控制器/方法名
     *  默认 格式： 控制器/方法名
     * @param $pathInfo
     * @return string
     */
    public static function getRoutePath($pathInfo)
    {
        $pathInfo = explode("/", ltrim($pathInfo, "/"));
        $routePath = '\\App\\Controller';
        switch ($pathInfo[0]) {
            // 其他路由
            default :
                $pathInfo[1] = !empty($pathInfo[1]) ? $pathInfo[1] : 'V1';
                $pathInfo[2] = !empty($pathInfo[2]) ? $pathInfo[2] : 'Index';
                $pathInfo[3] = !empty($pathInfo[3]) ? $pathInfo[3] : 'index';
                $routePath .= '\\' . ucwords($pathInfo[0]) . '\\' . ucwords($pathInfo[1]) . '\\' . ucwords($pathInfo[2]) . '@' . $pathInfo[3];
        }
        return $routePath;
    }
}