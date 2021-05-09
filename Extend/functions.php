<?php
/**
 * 公用方法
 */

if (!function_exists('friendlyDate')) {
    /**
     * 友好的时间显示
     *
     * @param int $sTime 待显示的时间
     * @param string $type 类型. normal | mohu | full | ymd | other
     * @param string $alt 已失效
     * @return string
     */
    function friendlyDate($sTime, $type = 'normal', $alt = 'false')
    {
        if (!$sTime)
            return '';

        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime = time();
        $dTime = $cTime - $sTime;
        $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
        $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if ($type == 'normal') {
            if ($dTime < 60) {
                if ($dTime < 10) {
                    return '刚刚';
                } else {
                    return intval(floor($dTime / 10) * 10) . "秒前";
                }
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dYear == 0 && $dDay == 0) {
                return '今天' . date('H:i', $sTime);
            } elseif ($dYear == 0) {
                return date("m月d日 H:i", $sTime);
            } else {
                return date("Y-m-d H:i", $sTime);
            }
        } elseif ($type == 'mohu') {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dDay > 0 && $dDay <= 7) {
                return intval($dDay) . "天前";
            } elseif ($dDay > 7 && $dDay <= 30) {
                return intval($dDay / 7) . '周前';
            } elseif ($dDay > 30) {
                return intval($dDay / 30) . '个月前';
            }
        } elseif ($type == 'full') {
            return date("Y-m-d , H:i:s", $sTime);
        } elseif ($type == 'ymd') {
            return date("Y-m-d", $sTime);
        } else {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dYear == 0) {
                return date("Y-m-d H:i:s", $sTime);
            } else {
                return date("Y-m-d H:i:s", $sTime);
            }
        }
    }
}


if (!function_exists('getRequest')) {
    /**
     * @param string $url
     * @param array $header
     * @return mixed
     */
    function getRequest($url, $header = [])
    {
        //初始化
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        // 执行后不直接打印出来
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 不从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 请求头，可以传数组
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);

        return $output;
    }
}

if (!function_exists('postRequest')) {
    /**
     * @param string $url
     * @param array $postData
     * @param array $header
     * @return mixed
     */
    function postRequest($url, $postData, $header = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        // 执行后不直接打印出来
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // 设置请求方式为post
        curl_setopt($ch, CURLOPT_POST, true);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        // 请求头，可以传数组
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // 不从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }
}

if (!function_exists('httpRequest')) {
    /**
     * 发送请求集合
     * @param $url
     * @param $method
     * @param array $postData
     * @param array $headers
     * @param bool $ssl
     * @param array $ssl_config
     * @param bool $debug
     * @return mixed
     */
    function httpRequest($url, $method, $postData = [], $headers = [], $ssl = false, $ssl_config = [], $debug = false)
    {
        // 将方法统一换成大写
        $method = strtoupper($method);
        // 初始化
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
        // 在发起连接前等待的时间，如果设置为0，则无限等待
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        // 设置CURL允许执行的最长秒数
        curl_setopt($curl, CURLOPT_TIMEOUT, 7);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, true);
                if (!empty($postData)) {
                    $tmpdatastr = is_array($postData) ? http_build_query($postData) : $postData;
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $tmpdatastr);
                }
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        if ($ssl == true) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
            // 严格校验
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            // 设置证书
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');

            $certPemPath = realpath($ssl_config["certPemPath"]);
            $keyPemPath = realpath($ssl_config["keyPemPath"]);

            curl_setopt($curl, CURLOPT_SSLCERT, $certPemPath);
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLKEY, $keyPemPath);
        }


        // 启用时会将头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        // 指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的
        curl_setopt($curl, CURLOPT_MAXREDIRS, 2);

        // 添加请求头部
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        // COOKIE带过去
//        curl_setopt($curl, CURLOPT_COOKIE, $Cookiestr);
        $response = curl_exec($curl);
        $requestInfo = curl_getinfo($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response_error = curl_error($curl);


        // 开启调试模式就返回 curl 的结果
        if ($debug) {
            echo "=====post data======\r\n";
            dump($postData);
            echo "=====info===== \r\n";
            dump($requestInfo);
            echo "=====response=====\r\n";
            dump($response);
            echo "=====http_code=====\r\n";
            dump($http_code);
            echo "=====http_error=====\r\n";
            dump($response_error);

            dump(curl_getinfo($curl, CURLINFO_HEADER_OUT));
        }
        curl_close($curl);
        return $response;
    }
}


if (!function_exists('checkFilePath')) {
    /**
     * 检测文件是否存在并且创建文件
     * @param $path
     * @return bool
     */
    function checkFilePath($path)
    {
        if (!is_dir($path)) {
            if (mkdir($path, 0777, true)) {
                chmod($path, 0777);
                if (!is_writable($path)) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('getTree')) {
    /**
     * 生成结构树
     * @param $node_info
     * @return array|bool
     */
    function getTree($node_info)
    {
        $level_info = [];
        $level_tree = [];
        if (!empty($node_info)) {
            // 先将键值对上
            foreach ($node_info as $value) {
                $level_info[$value['id']] = $value;
                $level_info[$value['id']]['sub_item'] = [];
            }
            // 编辑菜单
            foreach ($level_info as $key => $item) {
                // 如果是根菜单就直接添加 数组的地址，
                // 之后这个数组里面填加的内容都会同步进去
                if ($item["parent_id"] == 0) {
                    $level_tree[] = &$level_info[$key];
                } else {
                    // 不是根目录就向它的父级添加内容
                    $level_info[$item['parent_id']]['sub_item'][] = &$level_info[$key];
                }
            }
        }
        return $level_tree;
    }
}

if (!function_exists('objToArray')) {
    /**
     * 将对象转换成数组
     * @param $obj
     * @return mixed
     */
    function objToArray($obj)
    {
        return json_decode(json_encode($obj, JSON_UNESCAPED_UNICODE), true);
    }
}

if (!function_exists('exportCsv')) {
    /**
     * 导出cvs文件
     * @param $filePath
     * @param $thead
     * @param $tbody
     */
    function exportCsv($filePath, $thead, $tbody)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        // 检测并创建文件
        checkFilePath(dirname($filePath));
        $index = 0;
        $fp = fopen($filePath, 'w'); //生成临时文件
        foreach ($thead as $key => $val) {
            $thead[$key] = iconv('utf-8', 'gbk', $val);
        }
        fputcsv($fp, $thead);

        // 处理导出数据
        foreach ($tbody as $key => &$val) {
            foreach ($val as $k => $v) {
                $val[$k] = iconv('utf-8', 'gbk', $v) . "\t";
                // 每次写入10000条数据清除内存
                if ($index == 10000) {
                    $index = 0;
                    ob_flush();//清除内存
                    flush();
                }
                $index++;
            }
            fputcsv($fp, $val);
        }
        ob_flush();
        // 关闭句柄
        fclose($fp);
        header("Cache-Control: max-age=0");
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . basename($filePath));
        header("Content-Type: text/csv");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($filePath));
        // 输出文件
        @readfile($filePath);
        // 删除压缩包临时文件
        unlink($filePath);
        return;
    }
}

if (!function_exists('numberFormat')) {
    /**
     * 格式化金钱，最后一位四舍五入
     * @param $number
     * @param $precision
     * @return string
     */
    function numberFormat($number, $precision = 2)
    {
        return number_format($number, $precision, ".", "");
    }
}


if (!function_exists('privateSign')) {
    /**
     * 私钥
     * @param $data
     * @param $priKey
     * @param string $signType
     * @param string $encodeType
     * @return string
     */
    function privateSign($data, $priKey, $signType = "RSA", $encodeType = "SHA")
    {
        // 完善支付密钥参数
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查 RSA 私钥配置');
//        $res = openssl_pkey_get_private($res);

        if ("RSA2" == $signType) {
            if ($encodeType == "SHA") {
                // OPENSSL_ALGO_SHA256 是 php5.4.8 以上版本才支持
                openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
            } else if ($encodeType == "MD5") {
                //MD5WithRSA私钥加密
                openssl_sign($data, $sign, $res, OPENSSL_ALGO_MD5);
            }
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }
}

if (!function_exists('publicSign')) {
    /**
     * 公钥信息
     * @param $data
     * @param $priKey
     * @param string $encodeType
     * @return int
     */
    function publicSign($data, $priKey, $encodeType = "SHA")
    {
        // 完善支付密钥参数
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('您使用的公钥格式错误，请检查 公钥配置');


        $pubkey = openssl_pkey_get_public($res);

        if ($encodeType == "SHA") {
            // OPENSSL_ALGO_SHA256 是 php5.4.8 以上版本才支持
            $sign = openssl_verify($data, base64_decode($pubkey), $pubkey, OPENSSL_ALGO_SHA256);
        } else if ($encodeType == "MD5") {
            // MD5WithRSA解密
            $sign = openssl_verify($data, base64_decode($pubkey), $pubkey, OPENSSL_ALGO_MD5);
        }

        return $sign;
    }

    if (!function_exists('camelize')) {
        /**
         * 下划线转驼峰
         * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
         * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
         * @param $unCamelizeWords
         * @param string $separator
         * @return string
         */
        function camelize($unCamelizeWords, $separator = '_')
        {
            $unCamelizeWords = $separator . str_replace($separator, " ", strtolower($unCamelizeWords));
            return ltrim(str_replace(" ", "", ucwords($unCamelizeWords)), $separator);
        }
    }

    if (!function_exists('unCamelize')) {
        /**
         * 驼峰命名转下划线命名
         * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
         * @param $camelCaps
         * @param string $separator
         * @return string
         */
        function unCamelize($camelCaps, $separator = '_')
        {
            return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
        }
    }

    if (!function_exists('nonceStr')) {
        /**
         * 生成随机数
         * @return string
         */
        function nonceStr()
        {
            return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }
    }

    if (!function_exists('array2GBK')) {
        /**
         * 数组编码成GBK
         * @param $param
         * @return array
         */
        function array2GBK($param)
        {
            $param = array_map(function ($value) {
                return iconv('utf-8', 'gbk', $value);
            }, $param);

            return $param;
        }
    }

    if (!function_exists('sortAndCombine')) {
        /**
         * 排序数组并拼接成字符串
         * @param $data
         * @return string
         */
        function sortAndCombine($data)
        {
            $combineStr = '';
            ksort($data);
            // 处理数据
            foreach ($data as $key => $value) {
                $combineStr .= "{$key}={$value}&";
            }
            $combineStr = rtrim($combineStr, "&");
            return $combineStr;
        }
    }

    if (!function_exists('arrayToXml2')) {
        /**
         * 数组转换成xml 标签，不带xml 头部
         * @param $data
         * @param bool $eIsArray
         * @param $xmlObj
         * @return string
         */
        function arrayToXml2($data, $eIsArray = FALSE, $xmlObj = "")
        {
            if (!empty($xmlObj)) {
                $xml = $xmlObj;
            } else {
                $xml = new \XmlWriter();
            }

            if (!$eIsArray) {
                $xml->openMemory();
            }

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $xml->startElement($key);
                    arrayToXml2($value, TRUE, $xml);
                    $xml->endElement();
                    continue;
                }
                $xml->writeElement($key, $value);
            }

            if (!$eIsArray) {
                $xml->endElement();
                return $xml->outputMemory(true);
            }
        }
    }

    if (!function_exists('xml2array')) {
        /**
         * 将XML格式字符串转换为array
         * @param string $str XML格式字符串
         * @return array
         * @throws \Exception
         */
        function xml2array($str)
        {
            //禁止引用外部xml实体
            libxml_disable_entity_loader(true);

            $xml = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
            $json = json_encode($xml);
            $result = array();
            // value，一个字段多次出现，结果中的value是数组
            $bad_result = json_decode($json, TRUE);

            if (!empty($bad_result)) {
                foreach ($bad_result as $k => $v) {
                    if (is_array($v)) {
                        if (count($v) == 0) {
                            $result[$k] = '';
                        } else if (count($v) == 1) {
//                    $result[$k] = $v[0];
                            $result[$k] = current($v);
                        } else {
                            throw new \Exception('Duplicate elements in XML. ' . $str);
                        }
                    } else {
                        $result[$k] = $v;
                    }
                }
            }
            return $result;
        }
    }

    if (!function_exists('getDistance')) {
        /**
         * 计算两点之间的距离
         * @param $lng1 经度1
         * @param $lat1 纬度1
         * @param $lng2 经度2
         * @param $lat2 纬度2
         * @param int $unit m，km
         * @param int $decimal 位数
         * @return float
         */
        function getDistance($lng1, $lat1, $lng2, $lat2, $unit = 2, $decimal = 2)
        {

            $EARTH_RADIUS = 6370.996; // 地球半径系数
            $PI = 3.1415926535898;

            $radLat1 = $lat1 * $PI / 180.0;
            $radLat2 = $lat2 * $PI / 180.0;

            $radLng1 = $lng1 * $PI / 180.0;
            $radLng2 = $lng2 * $PI / 180.0;

            $a = $radLat1 - $radLat2;
            $b = $radLng1 - $radLng2;

            $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
            $distance = $distance * $EARTH_RADIUS * 1000;

            if ($unit === 2) {
                $distance /= 1000;
            }
            return round($distance, $decimal);
        }

    }

    if (!function_exists('xmlToObject')) {
        //xml格式转object
        function xmlToObject($xmlStr)
        {
            if (!is_string($xmlStr) || empty($xmlStr)) {
                return false;
            }
            // 由于解析xml的时候，即使被解析的变量为空，依然不会报错，会返回一个空的对象，所以，我们这里做了处理，当被解析的变量不是字符串，或者该变量为空，直接返回false
            $postObj = simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $postObj = json_decode(json_encode($postObj));
            //将xml数据转换成对象返回
            return $postObj;
        }
    }

    if (!function_exists('charset')) {
        /**
         * 更改字符编码
         * @param $param
         * @param $target
         * @param $source
         * @return null|string|string[]
         */
        function charset($param, $target, $source)
        {
            if (strcasecmp($source, $target) != 0) {
                $param = mb_convert_encoding($param, $target, $source);
            }
            return $param;
        }
    }


}