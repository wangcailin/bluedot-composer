<?php

namespace Composer\Support\Database\Models\Traits;

use Illuminate\Support\Str;

trait UuidPrimaryKey
{

    protected static function booting()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }
    public function getKeyType()
    {
        return 'string';
    }
}
