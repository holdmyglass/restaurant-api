<?php

namespace Modules\Product\DTO;

use Modules\Product\Enums\ProductOptionTypeEnum;

class ProductOptionDTO
{
    public function __construct(
        public array $name,
        public array $description,
        public string $slug,
        public ProductOptionTypeEnum $type,
        public ?int $min,
        public ?int $max,
        public ?bool $isActive
    ) {}

    public function toArrayExceptTranslatable(): array
    {
        return [
            'slug' => $this->slug,
            'type' => $this->type,
            'min' => $this->min,
            'max' => $this->max,
            'is_active' => $this->isActive,
        ];
    }
}
