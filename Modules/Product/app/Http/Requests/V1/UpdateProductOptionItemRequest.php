<?php

namespace Modules\Product\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Shared\Rules\TranslatableFieldRule;

class UpdateProductOptionItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'array',
                new TranslatableFieldRule('all'), // all languages required
            ],
            'description' => [
                'array',
                new TranslatableFieldRule('noneOrAll'), // either all or none required
            ],
            'max' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],
            'is_active' => [
                'sometimes',
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('product::messages.product_option.name_required'),
            'name.array' => __('product::messages.product_option.name_must_be_array'),
            'description.array' => __('product::messages.product_option.description_must_be_array'),
            'description.noneOrAll' => __('product::messages.product_option.description_none_or_all_required'),
            'max.required' => __('product::messages.product_option.max_required'),
            'max.integer' => __('product::messages.product_option.max_must_be_integer'),
            'max.min' => __('product::messages.product_option.max_must_be_at_least_1'),
            'is_active.required' => __('product::messages.product_option.is_active_required'),
            'is_active.boolean' => __('product::messages.product_option.is_active_must_be_boolean'),
        ];
    }
}
