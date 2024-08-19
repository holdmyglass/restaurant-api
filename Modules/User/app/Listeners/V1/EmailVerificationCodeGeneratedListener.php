<?php

namespace Modules\User\Listeners\V1;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\SendEmailVerificationCode;
use Modules\User\Events\V1\EmailVerificationCodeGenerated;

class EmailVerificationCodeGeneratedListener
{
    public function __construct(private Mail $mail) {}

    public function handle(EmailVerificationCodeGenerated $event)
    {
        Mail::to($event->user->email)->send(new SendEmailVerificationCode($event->user, $event->token, $event->appService));
    }
}
