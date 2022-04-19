<?php

namespace App\Models\DataDownload;

use Illuminate\Database\Eloquent\Model;

class DataDownload extends Model
{
    protected $table = 'data_download';

    protected $fillable = [
        'appid',
        'name',
        'tag_ids',
        'file',
        'category_id',
        'description',
    ];

    protected $casts = [
        'file' => 'json',
        'tag_ids' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(DataDownloadCategory::class, 'category_id', 'id');
    }
}
