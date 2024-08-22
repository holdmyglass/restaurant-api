<?php

namespace Modules\User\Listeners\V1;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\SendEmailVerifiedNotice;
use Modules\User\Events\V1\EmailVerified;

class EmailVerifiedListener
{
    public function handle(EmailVerified $event)
    {
        Mail::to($event->user->email)->send(new SendEmailVerifiedNotice($event->user, $event->appService));
    }
}
