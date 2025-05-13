<?php

namespace BluedotComposer\Support\Mail;

use BluedotComposer\Support\Mail\Login\Verify;
use Illuminate\Support\Facades\Mail;

class Client
{
    public static function sendBackendLoginCode($email, $code)
    {
        Mail::to($email)->queue(new Verify($code));
    }
}
