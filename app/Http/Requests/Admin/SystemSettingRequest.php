<?php

namespace App\Http\Requests\Admin;

use App\Enums\Types\PaymentGateway;
use App\Rules\EnumValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SystemSettingRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'system_status' => 'required|boolean',
            'wallet_module_status' => 'required|boolean',
            'payment_module_status' => 'required|boolean',
            'mail_module_status' => 'required|boolean',
            'login_module_status' => 'required|boolean',
            'register_module_status' => 'required|boolean',

            'payment_gateway' => ['required', EnumValidator::create(enum: PaymentGateway::class)],

            'module_maintenance_message' => 'required|string',
            'system_maintenance_message' => 'required|string',
        ];
    }
}
