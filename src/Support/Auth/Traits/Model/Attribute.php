<?php

namespace Composer\Support\Auth\Traits\Model;

use Composer\Support\Crypt\AES;

trait Attribute
{
    public function getPlaintextPhoneAttribute()
    {
        return $this->getPlaintext($this->phone);
    }

    public function getPlaintextEmailAttribute()
    {
        return $this->getPlaintext($this->email);
    }

    public function getMaskPhoneAttribute()
    {
        return $this->getMaskPhone($this->plaintext_phone);
    }

    public function getMaskEmailAttribute()
    {
        return $this->getMaskEmail($this->plaintext_email);
    }

    private function getPlaintext($value)
    {
        if ($value) {
            return  AES::decode($value);
        }
        return $value;
    }

    private function getCiphertext($value)
    {
        if ($value) {
            return  AES::encode($value);
        }
        return $value;
    }

    private function getMaskPhone($value)
    {
        if ($value) {
            return desensitize($value);
        }
        return $value;
    }

    private function getMaskEmail($value)
    {
        if ($value) {
            return desensitize($value);
        }
        return $value;
    }
}
