<?php


namespace App\Controller\Api\V1;


use App\Common\Controller\BaseController;


class Login extends BaseController
{

    public function miniAuth()
    {
        $input = $this->input;

        return $this->responseSuccess('获取用户信息成功');
    }

}