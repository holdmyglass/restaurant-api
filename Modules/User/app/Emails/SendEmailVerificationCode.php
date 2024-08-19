<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Shared\Services\V1\AppService;
use Modules\User\Models\User;

class SendEmailVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public string $token;

    public string $url;

    public string $appName;

    public string $appSupportEmail;

    public string $appSupportPhone;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $token, AppService $ai)
    {
        $this->user = $user;
        $this->token = $token;
        $this->appName = $ai->getName();
        $this->appSupportEmail = $ai->getSupportEmail();
        $this->appSupportPhone = $ai->getSupportEmail();
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $locale = app()->getLocale() ?? app()->getFallbackLocale();

        return $this->subject(__('user::messages.email.subject_email_verification_code'))->view("user::emails.{$locale}.send_email_verification_code");
    }
}
