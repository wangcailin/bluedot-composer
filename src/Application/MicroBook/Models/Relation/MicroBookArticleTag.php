<?php

namespace Composer\Application\MicroBook\Models\Relation;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MicroBookArticleTag extends Pivot
{
    protected $table = 'microbook_article_tag_relation';

    protected $primaryKey = 'article_id';
    public $incrementing = false;

    protected $fillable = [
        'tag_id',
        'article_id',
    ];
}
