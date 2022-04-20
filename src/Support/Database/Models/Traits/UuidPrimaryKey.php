<?php

namespace Composer\Support\Database\Models\Traits;

use Illuminate\Support\Str;

trait UuidPrimaryKey
{
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::createing(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
