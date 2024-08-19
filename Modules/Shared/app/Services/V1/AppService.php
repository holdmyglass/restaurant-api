<?php

namespace Modules\Shared\Services\V1;

class AppService
{
    private $supportEmail = 'help@articleinpire.com';

    private $name = 'Article Inspire';

    private $frontend_url = 'http://localhost:3000';

    public function getSupportEmail(): ?string
    {
        return $this->supportEmail;
    }

    public function setSupportEmail(string $value): void
    {
        $this->supportEmail = $value;
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
