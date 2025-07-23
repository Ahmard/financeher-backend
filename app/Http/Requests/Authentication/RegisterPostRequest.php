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
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'email' => EmailValidator::rules(uniqueEmail: true),
            'mobile_number' => ['required', PhoneNumberValidator::create(), 'unique:users,mobile_number'],
            'password' => ['required', PasswordValidator::create()],
            'country_id' => 'required|uuid|exists:geo_countries,id',
            'business_type_ids' => 'required|array|min:1',
            'business_type_ids.*' => 'required|uuid|exists:business_types,id',
            'business_stage_ids' => 'required|array|min:1',
            'business_stage_ids.*' => 'required|uuid|exists:business_stages,id',
            'opportunity_type_ids' => 'required|array|min:1',
            'opportunity_type_ids.*' => 'required|uuid|exists:opportunity_types,id',
        ];
    }
}
