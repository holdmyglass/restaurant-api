<?php

namespace Modules\Product\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Shared\Rules\TranslatableFieldRule;

class CreateProductOptionItemRequest extends FormRequest
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
            'min' => [
                'required',
                'integer',
                'min:0',
            ],
            'max' => [
                'required',
                'integer',
                'gte:min',
            ],
            'available' => [
                'required',
                'boolean',
            ],
            'vat' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('product::messages.product_option_item.name_required'),
            'name.array' => __('product::messages.product_option_item.name_must_be_array'),
            'description.array' => __('product::messages.product_option_item.description_must_be_array'),
            'description.noneOrAll' => __('product::messages.product_option_item.description_none_or_all_required'),
            'min.required' => __('product::messages.product_option_item.min_required'),
            'min.integer' => __('product::messages.product_option_item.min_must_be_integer'),
            'min.min' => __('product::messages.product_option_item.min_must_be_at_least_0'),
            'max.required' => __('product::messages.product_option_item.max_required'),
            'max.integer' => __('product::messages.product_option_item.max_must_be_integer'),
            'max.gte' => __('product::messages.product_option_item.max_must_be_greater_or_equal_min'),
            'available.required' => __('product::messages.product_option_item.available_required'),
            'available.boolean' => __('product::messages.product_option_item.available_must_be_boolean'),
            'vat.required' => __('product::messages.product_option_item.vat.required'),
            'vat.numeric' => __('product::messages.product_option_item.vat.numeric'),
        ];
    }
}
