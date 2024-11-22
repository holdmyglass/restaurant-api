<?php

namespace Modules\Product\DTO;

class ProductCategoryDTO
{
    public function __construct(
        public readonly ?string $parentId,
        public readonly array $name,
        public readonly array $description,
        public readonly ?string $image,
        public readonly string $slug,
        public readonly int $rank,
        public readonly ?string $type,
    ) {}

    public function toArray(): array
    {
        return [
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'slug' => $this->slug,
            'rank' => $this->rank,
            'type' => $this->type,
        ];
    }
}
