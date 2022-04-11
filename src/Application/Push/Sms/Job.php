<?php

namespace Composer\Application\Push\Sms;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    use JobTrait;

    public function handle()
    {
        $this->sendSmsHandle($this->query);
    }
}
