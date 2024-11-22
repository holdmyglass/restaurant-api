<?php

namespace Modules\Product\DTO;

class ProductDTO
{
    public function __construct(
        public readonly array $name,
        public readonly array $description,
        public readonly ?string $image,
        public readonly string $slug,
        public readonly int $rank,
        public readonly bool $available,
        public readonly bool $takeaway,
        public readonly bool $delivery,
        public readonly bool $eatIn,
        public readonly bool $offer,
        public readonly int $vat
    ) {}

    public function toArrayExceptTranslatable(): array
    {
        return [
            'image' => $this->image,
            'slug' => $this->slug,
            'rank' => $this->rank,
            'available' => $this->available,
            'takeaway' => $this->takeaway,
            'delivery' => $this->delivery,
            'eat_in' => $this->eatIn,
            'offer' => $this->offer,
            'vat' => $this->vat * 100,
        ];
    }
}
