<?php

namespace Modules\Product\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionResource extends JsonResource
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
            'mix' => $this->min,
            'max' => $this->max,
            'isActive' => $this->is_active,
            'type' => $this->type,
        ];
    }
}
