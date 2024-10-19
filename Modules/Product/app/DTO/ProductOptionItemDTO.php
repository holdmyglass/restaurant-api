<?php

namespace Modules\Product\DTO;

class ProductOptionItemDTO
{
    public function __construct(
        public array $name,
        public array $description,
        public ?int $max,
        public ?bool $isActive
    ) {}

    public function toArrayExceptTranslatable(): array
    {
        return [
            'max' => $this->max,
            'is_active' => $this->isActive,
        ];
    }
}
