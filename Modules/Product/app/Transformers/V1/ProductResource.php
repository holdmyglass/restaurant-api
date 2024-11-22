<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\File\Enums\ImageSizeEnum;
use Modules\File\Services\V1\ImageService;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $imageService = new ImageService;

        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'slug' => $this->slug,
            'image' => $this->image ? [
                'avatar' => $imageService->getImageUrl(ImageSizeEnum::AVATAR, $this->image),
                'small-square' => $imageService->getImageUrl(ImageSizeEnum::SMALLSQUARE, $this->image),
                'medium-square' => $imageService->getImageUrl(ImageSizeEnum::MEDIUMSQUARE, $this->image),
            ] : null,
            'price' => PriceResource::collection($this->getDistinctPrices()),
            'options' => ProductOptionResource::collection($this->options),
            'rank' => $this->rank,
            'available' => $this->available,
            'categories' => $this->categoryArrayWithnameAndID(),
        ];
    }
}
