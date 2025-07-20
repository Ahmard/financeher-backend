<?php

namespace Database\Seeders;

use App\Enums\Types\OpportunityTypeCode;
use App\Services\OpportunityTypeService;
use App\Services\UserService;
use Illuminate\Database\Seeder;

class OpportunityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(OpportunityTypeService $service): void
    {
        foreach (OpportunityTypeCode::cases() as $case) {
            $service->create(
                createdBy: UserService::SUPER_ADMIN_ACCOUNT_ID,
                name: $case->getDisplayName(),
                code: $case->value,
                desc: $case->getDescription()
            );
        }
    }
}