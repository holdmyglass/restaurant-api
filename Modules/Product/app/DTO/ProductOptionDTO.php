<?php

namespace Modules\Product\DTO;

use Modules\Product\Enums\ProductOptionTypeEnum;

class ProductOptionDTO
{
    public function __construct(
        public readonly array $name,
        public readonly array $description,
        public readonly string $slug,
        public readonly ProductOptionTypeEnum $type,
        public readonly ?int $min,
        public readonly ?int $max,
        public readonly ?bool $active,
    ) {}

    public function toArrayExceptTranslatable(): array
    {
        return [
            'slug' => $this->slug,
            'type' => $this->type,
            'min' => $this->min,
            'max' => $this->max,
            'active' => $this->active,
        ];
    }
}
