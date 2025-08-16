<?php

namespace App\Http\Requests\Authentication;

use App\Rules\EmailValidator;
use App\Rules\PasswordValidator;
use App\Rules\PhoneNumberValidator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|min:5|max:150',
            'email' => EmailValidator::rules(uniqueEmail: true),
            'mobile_number' => ['nullable', PhoneNumberValidator::create(), 'unique:users,mobile_number'],
            'password' => ['required', PasswordValidator::create()],
            'country_id' => 'nullable|uuid|exists:geo_countries,id',
            'industry_id' => 'nullable|uuid|exists:industries,id',
        ];
    }
}
