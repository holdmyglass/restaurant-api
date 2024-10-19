<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'description' => $this->getTranslations('description'),
            'slug' => $this->slug,
            'image' => $this->image,
            'price' => $this->regularPrice()->isNotEmpty() ? $this->regularPrice() : null,
            'offerPrice' => $this->offerPrice()->isNotEmpty() ? $this->offerPrice() : null,
        ];
    }
}
