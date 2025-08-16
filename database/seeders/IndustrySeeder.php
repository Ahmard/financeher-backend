<?php

namespace Database\Seeders;

use App\Enums\Types\IndustryCode;
use App\Services\IndustryService;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(IndustryService $service): void
    {
        // Education
        foreach (IndustryCode::cases() as $case) {
            $service->create(
                createdBy: UserService::SUPER_ADMIN_ACCOUNT_ID,
                name: $case->getDisplayName(),
                code: $case->value,
                desc: $case->getDescription()
            );
        }
    }
}
