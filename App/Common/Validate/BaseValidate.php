<?php

namespace App\Common\Validate;


abstract class BaseValidate
{
    public $validate;

    public function validate($validateData, $actionName)
    {
        if (!method_exists(get_class($this), $actionName)) {
            return true;
        }
        $this->validate = new \EasySwoole\Validate\Validate();
        $this->$actionName();
        if (!$this->validate->validate($validateData)) {
            return $this->validate->getError()->__toString();
        }
        return true;
    }
}