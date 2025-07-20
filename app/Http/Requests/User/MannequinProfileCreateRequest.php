<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MannequinProfileCreateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'height' => 'required|numeric',
            'chest_width' => 'required|numeric',
            'waist_width' => 'required|numeric',
            'hip_width' => 'required|numeric',
            'shoulder_width' => 'required|numeric',
            'arm_length' => 'required|numeric',
            'leg_length' => 'required|numeric',
            'neck_length' => 'required|numeric',
        ];
    }
}
