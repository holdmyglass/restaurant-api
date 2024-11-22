<?php

namespace Modules\Product\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Enums\PriceTypeEnum;
use Modules\Shared\Enums\CurrencyEnum;
use Modules\Shared\Rules\TranslatableFieldRule;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => [
                'sometimes',
                'array',
            ],
            'category.*' => [
                'uuid',
            ],
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
                'image',
                'mimes:png,jpg',
            ],
            'rank' => [
                'sometimes',
                'integer',
                'gt:0',
            ],
            'price' => [
                'nullable',
                'array',
            ],
            'price.*' => [
                'required',
                'array',
            ],
            'price.*.currency' => [
                'required',
                Rule::enum(CurrencyEnum::class),
            ],
            'price.*.price_type' => [
                'required',
                Rule::enum(PriceTypeEnum::class),
            ],
            'price.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'price.*.valid_from' => [
                'nullable',
                'date',
            ],
            'price.*.valid_until' => [
                'nullable',
                'date',
            ],
            'vat' => [
                'required',
                'numeric',
                'min:0',
            ],
            'available' => [
                'required',
                'boolean',
            ],
            'takeaway' => [
                'required',
                'boolean',
            ],
            'delivery' => [
                'required',
                'boolean',
            ],
            'eat_in' => [
                'required',
                'boolean',
            ],
            'offer' => [
                'required',
                'boolean',
            ],
            'valid_from' => [
                'sometimes',
                'nullable',
                'date',
            ],
            'valid_until' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category.array' => __('product::messages.product.category_array'),
            'category.*.uuid' => __('product::messages.product.category_id_uuid'),
            'name.required' => __('product::messages.product.name_required'),
            'name.array' => __('product::messages.product.name_must_be_array'),
            'description.array' => __('product::messages.product.description_must_be_array'),
            'image.image' => __('product::messages.product.image_image'),
            'image.mimes' => __('product::messages.product.image_mimes'),
            'rank.integer' => __('product::messages.product.rank_positive_number'),
            'rank.gt' => __('product::messages.product.rank_positive_number'),
            'price.array' => __('product::messages.product.price_must_be_array'),
            'price.*.array' => __('product::messages.product.price_must_be_array'),
            'price.*.currency.required' => __('product::messages.product.price_currency_required'),
            'price.*.currency.enum' => __('product::messages.product.price_currency_invalid'),
            'price.*.price_type.required' => __('product::messages.product.price_type_required'),
            'price.*.price_type.enum' => __('product::messages.product.price_type_invalid'),
            'price.*.price.required' => __('product::messages.product.price_required'),
            'price.*.price.numeric' => __('product::messages.product.price_must_be_numeric'),
            'valid_from.date' => __('product::messages.product.price_valid_from_invalid'),
            'valid_until.date' => __('product::messages.product.price_valid_until_invalid'),
            'available.required' => __('product::messages.product.available_required'),
            'available.boolean' => __('product::messages.product.available_must_be_boolean'),
            'takeaway.required' => __('product::messages.product.takeaway_required'),
            'takeaway.boolean' => __('product::messages.product.takeaway_must_be_boolean'),
            'delivery.required' => __('product::messages.product.delivery_required'),
            'delivery.boolean' => __('product::messages.product.delivery_must_be_boolean'),
            'eat_in.required' => __('product::messages.product.eat_in_required'),
            'eat_in.boolean' => __('product::messages.product.eat_in_must_be_boolean'),
            'offer.required' => __('product::messages.product.offer_required'),
            'offer.boolean' => __('product::messages.product.offer_must_be_boolean'),
            'option.array' => __('product::messages.product.category_array'),
            'option.*.uuid' => __('product::messages.product.category_id_uuid'),
            'vat.required' => __('product::messages.product.vat.required'),
            'vat.numeric' => __('product::messages.product.vat.numeric'),
        ];
    }
}
