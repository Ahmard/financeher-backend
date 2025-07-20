<?php

namespace Database\Seeders;

use App\Enums\Types\BusinessStageCode;
use App\Services\BusinessStageService;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class BusinessStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(BusinessStageService $service): void
    {
        foreach (BusinessStageCode::cases() as $case) {
            $service->create(
                createdBy: UserService::SUPER_ADMIN_ACCOUNT_ID,
                name: $case->getDisplayName(),
                code: $case->value,
                desc: $case->getDescription()
            );
        }
    }
}
