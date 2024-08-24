<?php

namespace Modules\User\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Enums\RegisterOptionEnum;

class LoginRequest extends FormRequest
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
            'password' => [
                'required',
            ],
        ];

    }

    public function messages(): array
    {
        return [
            'identity.required' => __('user::messages.login.identity_required'),
            'identity.Illuminate\Validation\Rules\Enum' => __('user::messages.login.identity_enum'),
            'email.required_if' => __('user::messages.login.email_required'),
            'email.email' => __('user::messages.login.email_email'),
            'phone.required_if' => __('user::messages.login.phone_required'),
            'password.required' => __('user::messages.login.password_required'),
        ];
    }
}
