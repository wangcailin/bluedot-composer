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
            $this->validateCreateRules,
            $this->validateCreateMessage
        )->validate();
        $this->handleValidate();
    }

    public function handleUpdateValidate()
    {
        Validator::make(
            $this->data,
            $this->validateUpdateRules,
            $this->validateUpdateMessage
        )->validate();
        $this->handleValidate();
    }

    public function handleValidate()
    {
        Validator::make(
            $this->data,
            $this->validateRules,
            $this->validateMessage
        )->validate();
    }
}
