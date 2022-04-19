<?php

namespace Composer\Application\MicroBook\Models;

use Composer\Application\MicroBook\Models\Relation\MicroBookArticleTag;
use Composer\Application\Tag\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class MicroBookArticle extends Model
{
    protected $table = 'microbook_article';

    protected $fillable = [
        'category_ids',
        'appid',
        'title',
        'show_cover_pic',
        'url',
        'thumb_url',
        'voice',
        'qrcode',
        'created_at',
        'updated_at',
        'state',
        'create_time',
        'update_time',
        'media_id',
        'sort',
        'keywords'
    ];

    protected $casts = [
        'category_ids' => 'array',
        'keywords' => 'array',
    ];

    public function tag()
    {
        return $this->belongsToMany(Tag::class, MicroBookArticleTag::class, 'article_id', 'tag_id')->withTimestamps();
    }
}
