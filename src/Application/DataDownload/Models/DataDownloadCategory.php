<?php

namespace App\Models\DataDownload;

use Illuminate\Database\Eloquent\Model;

class DataDownloadCategory extends Model
{
    protected $table = 'data_download_category';

    protected $fillable = [
        'appid',
        'name',
        'tag_ids',
        'state',
        'parent_id',
        'sort',
    ];
}
