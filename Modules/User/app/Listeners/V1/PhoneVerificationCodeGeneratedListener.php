<?php

namespace Modules\User\Listeners\V1;

use Modules\Shared\Services\V1\SMSService;
use Modules\User\Events\V1\PhoneVerificationCodeGenerated;

class PhoneVerificationCodeGeneratedListener
{
    public function __construct(private SMSService $smsService) {}

    public function handle(PhoneVerificationCodeGenerated $event)
    {
        // here send sms
        $this->smsService->send($event->user->phone, $event->token);
    }
}
