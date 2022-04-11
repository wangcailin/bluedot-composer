<?php

namespace Composer\Application\Push\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $config;
    public $to;
    public $subject;
    public $body;
    public $cc;

    public function __construct($config, $to, $subject, $body, $cc = [])
    {
        $this->config = $config;
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->cc = $cc;
    }

    use JobTrait;

    public function handle()
    {
        $this->sendMailHandle($this->config, $this->to, $this->subject, $this->body, $this->cc);
    }
}
