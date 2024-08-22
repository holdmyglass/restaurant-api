<?php

namespace Modules\User\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Enums\RegisterOptionEnum;

class RegisterRequest extends FormRequest
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
            'full_name' => [
                'required',
            ],
            'email' => [
                'required_if:identity,'.RegisterOptionEnum::EMAIL->value,
                'email',
                'unique:users',
            ],
            'phone' => [
                'required_if:identity,'.RegisterOptionEnum::PHONE->value,
                'email',
                'unique:users',
            ],
            'password' => [
                'required',
                'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', 'confirmed',
            ],
            'terms' => ['accepted'],
        ];

    }

    public function messages(): array
    {
        return [
            'identity.required' => __('user::messages.register.identity_required'),
            'identity.Illuminate\Validation\Rules\Enum' => __('user::messages.register.identity_enum'),
            'full_name.required' => __('user::messages.register.full_name_required'),
            'email.required_if' => __('user::messages.register.email_required'),
            'email.email' => __('user::messages.register.email_email'),
            'email.unique' => __('user::messages.register.email_unique'),
            'phone.required_if' => __('user::messages.register.phone_required'),
            'phone.unique' => __('user::messages.register.phone_unique'),
            'password.required' => __('user::messages.register.password_required'),
            'password.min' => __('user::messages.register.password_min'),
            'password.regex' => __('user::messages.register.password_regex'),
            'password.confirmed' => __('user::messages.register.password_confirmed'),
            'terms.accepted' => __('user::messages.register.terms_accepted'),
        ];
    }
}
