<?php

namespace Composer\Application\Event\Live;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Job implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 3600;

    public $retryAfter = -1;

    public $filepath;
    public $eventId;

    public function __construct($filepath, $eventId)
    {
        $this->filepath = $filepath;
        $this->eventId = $eventId;
    }

    public function handle()
    {
        Log::info($this->filepath);
        Log::info($this->eventId);
        PPT::pdf2png($this->filepath, $this->eventId);
    }
}
