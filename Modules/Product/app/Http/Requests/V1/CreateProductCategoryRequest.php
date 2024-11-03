<?php

namespace Modules\Product\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Enums\ProductCategoryTypeEnum;
use Modules\Shared\Rules\TranslatableFieldRule;

class CreateProductCategoryRequest extends FormRequest
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
            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:png,jpg',
            ],
            'type' => [
                'sometimes',
                Rule::enum(ProductCategoryTypeEnum::class),
            ],
            'rank' => [
                'sometimes',
                'integer',
                'gt:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('product::messages.product_category.name_required'),
            'image.image' => __('product::messages.product_category.image_image'),
            'image.mimes' => __('product::messages.product_category.image_mimes'),
            'type.Illuminate\Validation\Rules\Enum' => __('product::messages.product_category.type_enum'),
            'rank.integer' => __('product::messages.product_category.rank_positive_number'),
            'rank.gt' => __('product::messages.product_category.rank_positive_number'),
        ];
    }
}
