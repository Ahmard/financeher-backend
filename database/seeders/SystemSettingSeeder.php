<?php

namespace Database\Seeders;

use App\Enums\Types\PaymentGateway;
use App\Models\Plan;
use App\Services\SystemSettingService;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(SystemSettingService $service): void
    {
        $service->create(
            createdBy: 2,
            data: [
                'system_status' => true,
                'login_module_status' => true,
                'register_module_status' => true,
                'mail_module_status' => true,
                'wallet_module_status' => true,
                'payment_module_status' => true,
                'payment_gateway' => PaymentGateway::MONNIFY->lowercase(),

                'active_plan_id' => Plan::query()
                    ->select('id')
                    ->orderBy('created_at')
                    ->value('id'),

                'moniepoint_auth_token' => '{}',

                'module_maintenance_message' => file_get_contents(dirname(__DIR__) . '/raw/module_maintenance_message.txt'),
                'system_maintenance_message' => file_get_contents(dirname(__DIR__) . '/raw/system_maintenance_message.txt'),
            ]
        );
    }
}
