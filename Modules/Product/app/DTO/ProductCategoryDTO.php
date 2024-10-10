<?php

namespace Modules\Product\DTO;

class ProductCategoryDTO
{
    public function __construct(
        public ?string $parentId,
        public array $name,
        public array $description,
        public ?string $image,
        public string $slug,
        public int $rank,
        public ?string $type,
    ) {}
}
