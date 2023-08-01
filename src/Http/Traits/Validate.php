<?php

namespace Composer\Http\Traits;

use Illuminate\Support\Facades\Validator;

trait Validate
{
    /** 验证器 */
    protected $validateRules = [];
    protected $validateMessage = [];

    protected $validateCreateRules = [];
    protected $validateCreateMessage = [];

    protected $validateUpdateRules = [];
    protected $validateUpdateMessage = [];


    public function handleCreateValidate()
    {
        Validator::make(
            $this->data,
            $this->getValidateCreateRules(),
            $this->getValidateCreateMessage()
        )->validate();
        $this->handleValidate();
    }
    public function getValidateCreateRules()
    {
        return $this->validateCreateRules;
    }
    public function getValidateCreateMessage()
    {
        return $this->validateCreateMessage;
    }

    public function handleUpdateValidate()
    {
        Validator::make(
            $this->data,
            $this->getValidateUpdateRules(),
            $this->getValidateUpdateMessage()
        )->validate();
        $this->handleValidate();
    }
    public function getValidateUpdateRules()
    {
        return $this->validateUpdateRules;
    }
    public function getValidateUpdateMessage()
    {
        return $this->validateUpdateMessage;
    }

    public function getValidateRules()
    {
        return $this->validateRules;
    }

    public function handleValidate()
    {
        Validator::make(
            $this->data,
            $this->getValidateRules(),
            $this->validateMessage
        )->validate();
    }
}
