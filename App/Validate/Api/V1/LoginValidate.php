<?php


namespace App\Validate\Api\V1;

use App\Common\Validate\BaseValidate;


class LoginValidate extends BaseValidate
{
    public function miniAuth()
    {
        $this->validate->addColumn('code')->notEmpty('code 不能为空');
    }

}
