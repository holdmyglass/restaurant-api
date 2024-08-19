<?php

namespace Modules\Shared\Services\V1;

class EmailService
{
    public function getVerificationUrl(string $base64EncodedUrlData)
    {
        $url = env('FRONTEND_URL').'/auth/verify?token='.$base64EncodedUrlData;

        return $url;
    }

    public function getVerificationLink(string $email, $token)
    {
        $url = "/api/v1/auth/verify-email/$email/$token";

        return $url;
    }
}
