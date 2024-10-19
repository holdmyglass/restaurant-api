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
