<?php

namespace App\Http\Requests\Authentication;

use App\Rules\PasswordValidator;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PasswordResetPostRequest extends FormRequest
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
            'password' => ['required', 'confirmed', PasswordValidator::create()],
            'password_confirmation' => 'required|string|min:8|max:100',
        ];
    }
}
