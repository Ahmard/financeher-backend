<?php

namespace App\Http\Requests\Authorization;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PermissionPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'permissions.*' => 'required|string|min:3'
        ];
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return $this->validated('permissions');
    }
}
