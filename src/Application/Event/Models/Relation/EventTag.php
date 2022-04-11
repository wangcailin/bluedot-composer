<?php

namespace Composer\Application\Event\Models\Relation;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventTag extends Pivot
{
    protected $table = 'event_tag_relation';

}
