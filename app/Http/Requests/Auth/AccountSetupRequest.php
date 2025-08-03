<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AccountSetupRequest extends FormRequest
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
            'business_type_ids' => 'required|array|min:1',
            'business_type_ids.*' => 'required|uuid|exists:business_types,id',
            'business_stage_ids' => 'required|array|min:1',
            'business_stage_ids.*' => 'required|uuid|exists:business_stages,id',
            'opportunity_type_ids' => 'required|array|min:1',
            'opportunity_type_ids.*' => 'required|uuid|exists:opportunity_types,id',
        ];
    }
}
