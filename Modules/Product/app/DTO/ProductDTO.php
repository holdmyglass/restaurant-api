<?php

namespace Modules\Product\DTO;

class ProductDTO
{
    public function __construct(
        public array $name,
        public array $description,
        public ?string $image,
        public string $slug,
        public int $rank,
        public bool $available,
        public bool $takeaway,
        public bool $delivery,
        public bool $eatIn,
        public bool $offer
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
        ];
    }
}
