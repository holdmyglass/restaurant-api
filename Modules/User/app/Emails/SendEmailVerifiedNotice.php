<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Shared\Services\V1\AppService;
use Modules\User\Models\User;

class SendEmailVerifiedNotice extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $appName;

    public string $appSupportEmail;

    public string $appSupportPhone;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, AppService $appService)
    {
        $this->user = $user;
        $this->appName = $appService->getName();
        $this->appSupportEmail = $appService->getSupportEmail();
        $this->appSupportPhone = $appService->getSupportEmail();
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $locale = app()->getLocale() ?? app()->getFallbackLocale();

        return $this->subject(__('user::messages.email.subject_email_verified'))->view("user::emails.{$locale}.email_verified");
    }
}
