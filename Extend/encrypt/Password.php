<?php

namespace extend\encrypt;

class Password
{
    static $encrypt_key = '474d16730*069a839*';

    /**
     * 密码加密
     * @param $password
     * @return string
     */
    public static function encryptPassword($password)
    {
        $encrypt_password = md5(md5($password) . self::$encrypt_key);
        return $encrypt_password;
    }

    /**
     * 密码校验
     * @param string $password 原密码
     * @param string $candidate 校验密码
     * @return bool
     */
    public static function checkPassword($password, $candidate)
    {
        $candidate = self::encryptPassword($candidate);
        if ($password === $candidate) {
            return true;
        } else {
            return false;
        }
    }
}