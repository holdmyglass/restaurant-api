<?php

namespace Modules\Product\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Enums\ProductOptionTypeEnum;
use Modules\Shared\Rules\TranslatableFieldRule;

class UpdateProductOptionRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        // Dump the request data before validation
        \Log::info('Request Data:', $this->all());
    }

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
            'type' => [
                'required',
                Rule::enum(ProductOptionTypeEnum::class),
            ],
            'min' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],
            'max' => [
                'sometimes',
                'required',
                'integer',
                'gte:min',
            ],
            'active' => [
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
            'type.required' => __('product::messages.product_option.type_required'),
            'type.enum' => __('product::messages.product_option.type_invalid'),
            'min.required' => __('product::messages.product_option.min_required'),
            'min.integer' => __('product::messages.product_option.min_must_be_integer'),
            'min.min' => __('product::messages.product_option.min_must_be_at_least_1'),
            'max.required' => __('product::messages.product_option.max_required'),
            'max.integer' => __('product::messages.product_option.max_must_be_integer'),
            'max.gte' => __('product::messages.product_option.max_must_be_greater_or_equal_min'),
            'active.required' => __('product::messages.product_option.active_required'),
            'active.boolean' => __('product::messages.product_option.active_must_be_boolean'),
        ];
    }
}
