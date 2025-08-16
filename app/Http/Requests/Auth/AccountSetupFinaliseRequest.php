<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AccountSetupFinaliseRequest extends FormRequest
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
            'business_stage_id' => 'required|exists:business_stages,id',
            'opportunity_type_ids' => 'required|array',
            'opportunity_type_ids.*' => 'required|exists:opportunity_types,id',
            'industry_ids' => 'required|array',
            'industry_ids.*' => 'required|exists:industries,id',
        ];
    }
}
