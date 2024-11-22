<?php

namespace Modules\Product\DTO;

class ProductOptionItemDTO
{
    public function __construct(
        public readonly array $name,
        public readonly array $description,
        public readonly string $slug,
        public readonly ?int $min,
        public readonly ?int $max,
        public readonly ?bool $available,
        public readonly int $vat
    ) {}

    public function toArrayExceptTranslatable(): array
    {
        return [
            'slug' => $this->slug,
            'min' => $this->min,
            'max' => $this->max,
            'available' => $this->available,
            'vat' => $this->vat * 100,
        ];
    }
}
