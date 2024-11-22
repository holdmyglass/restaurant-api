<?php

namespace Modules\Product\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductOptionWithItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item' => [
                'present',
                'array',
            ],
            'item.*' => [
                'uuid',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'item.array' => __('product::messages.product_option.item_array'),
            'item.*.uuid' => __('product::messages.product_option.item_id_uuid'),
        ];
    }
}
