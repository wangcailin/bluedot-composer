<?php

namespace Composer\Application\Push\Sms;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Job implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use JobTrait;


    public $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function handle()
    {
        $this->sendSmsHandle($this->query);
    }
}
