<?php

namespace Composer\Application\System\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'system_config';

    protected $fillable = [
        'type',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public static function getMailConfig()
    {
        return self::where('type', 'mail')->first()['data'];
    }
}
