<?php

namespace Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\User\Events\V1\EmailVerificationCodeGenerated;
use Modules\User\Events\V1\EmailVerified;
use Modules\User\Events\V1\PhoneVerificationCodeGenerated;
use Modules\User\Listeners\V1\EmailVerificationCodeGeneratedListener;
use Modules\User\Listeners\V1\EmailVerifiedListener;
use Modules\User\Listeners\V1\PhoneVerificationCodeGeneratedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        EmailVerificationCodeGenerated::class => [
            EmailVerificationCodeGeneratedListener::class,
        ],
        PhoneVerificationCodeGenerated::class => [
            PhoneVerificationCodeGeneratedListener::class,
        ],
        EmailVerified::class => [
            EmailVerifiedListener::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
