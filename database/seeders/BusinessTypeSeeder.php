<?php

namespace Database\Seeders;

use App\Enums\Types\BusinessTypeCode;
use App\Services\BusinessTypeService;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(BusinessTypeService $service): void
    {
        // Education
        foreach (BusinessTypeCode::cases() as $case) {
            $service->create(
                createdBy: UserService::SUPER_ADMIN_ACCOUNT_ID,
                name: $case->getDisplayName(),
                code: $case->value,
                desc: $case->getDescription()
            );
        }
    }
}