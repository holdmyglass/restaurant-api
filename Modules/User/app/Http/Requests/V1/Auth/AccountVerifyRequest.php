<?php

namespace Modules\User\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Enums\RegisterOptionEnum;

class AccountVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identity' => [
                'required',
                Rule::enum(RegisterOptionEnum::class),
            ],
            'email' => [
                'required_if:identity,'.RegisterOptionEnum::EMAIL->value,
                'email',
            ],
            'phone' => [
                'required_if:identity,'.RegisterOptionEnum::PHONE->value,
            ],
            'token' => [
                'required',
            ],
        ];

    }

    public function messages(): array
    {
        return [
            'identity.required' => __('user::messages.verify.identity_required'),
            'identity.Illuminate\Validation\Rules\Enum' => __('user::messages.verify.identity_enum'),
            'email.required_if' => __('user::messages.verify.email_required'),
            'email.email' => __('user::messages.verify.email_email'),
            'phone.required_if' => __('user::messages.verify.phone_required'),
            'token.required' => __('user::messages.verify.token_required'),
        ];
    }
}
