<?php

namespace App\Service;

class RedisKeyService
{

    public static function userToken($data): string
    {
        return 'user_' . sha1(md5($data));
    }

    public static function tempUserToken($data): string
    {
        return 'temp_user_' . $data;
    }

    public static function accessToken($data): string
    {
        return 'access_token_' . $data;
    }

}