<?php

namespace extend\encrypt;

class Encrypt
{
    private static $key = 'wfUE3QgQ';
    private static $iv = 'w1L7JMyYaiuKAXGO';

    public static function encrypt($encrypt_msg)
    {
        $encrypted = openssl_encrypt($encrypt_msg, 'aes-256-cbc', self::$key, OPENSSL_RAW_DATA, self::$iv);
        return base64_encode($encrypted);
    }

    public static function decrypt($decrypt_msg)
    {
        $decrypt_msg = base64_decode($decrypt_msg);
        return openssl_decrypt($decrypt_msg, 'aes-256-cbc', self::$key, OPENSSL_RAW_DATA, self::$iv);
    }

    public static function check_encrypt($encrypt_msg, $decrypt_msg)
    {
        $encrypted = openssl_encrypt($encrypt_msg, 'aes-256-cbc', self::$key, OPENSSL_RAW_DATA, self::$iv);
        $encrypt_msg = base64_encode($encrypted);
        if ($encrypt_msg === $decrypt_msg) {
            return true;
        } else {
            return false;
        }
    }
}