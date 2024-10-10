<?php

namespace Modules\Shared\Services\V1;

/**
 * These data should be fetched from database for the specific tenant
 */
class AppService
{
    private $name = 'Sushi Merksem';

    private $frontend_url = 'http://localhost:3000';

    private $supportEmail = 'help@sushimerksem.be';

    private $supportPhone = '0032 484 63 01 51';

    public function getSupportEmail(): ?string
    {
        return $this->supportEmail;
    }

    public function setSupportEmail(string $email): void
    {
        $this->supportEmail = $email;
    }

    public function getSupportPhone(): ?string
    {
        return $this->supportPhone;
    }

    public function setSupportPhone(string $phone): void
    {
        $this->supportPhone = $phone;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $value): void
    {
        $this->name = $value;
    }

    public function getFrontendUrl(): ?string
    {
        return $this->frontend_url;
    }

    public function setFrontendUrl(string $value): void
    {
        $this->frontend_url = $value;
    }
}
