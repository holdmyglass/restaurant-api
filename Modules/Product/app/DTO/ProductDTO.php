<?php

namespace Modules\Product\DTO;

class ProductDTO
{
    public function __construct(
        public ?string $id,
        public string $name,
        public string $description,
        public ?string $image,
        public string $slug,
        public int $rank
    ) {}
}
