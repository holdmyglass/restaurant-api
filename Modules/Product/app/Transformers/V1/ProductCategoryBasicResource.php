<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\File\Enums\ImageSizeEnum;
use Modules\File\Services\V1\ImageService;

class ProductCategoryBasicResource extends JsonResource
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
            'rank' => $this->rank,
            'image' => $this->image ? [
                'avatar' => $imageService->getImageUrl(ImageSizeEnum::AVATAR, $this->image),
                'small-square' => $imageService->getImageUrl(ImageSizeEnum::SMALLSQUARE, $this->image),
                'medium-square' => $imageService->getImageUrl(ImageSizeEnum::MEDIUMSQUARE, $this->image),
            ] : null,
            'type' => $this->type,
            'dishesCount' => count($this->products),
        ];
    }
}