<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $category = $this->resource;

        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'translations' => [
                'name' => $category->getTranslations('name'),
                'description' => $category->getTranslations('description'),
            ],
            'rank' => $category->rank,
            'image' => $category->image,
        ];
    }
}
