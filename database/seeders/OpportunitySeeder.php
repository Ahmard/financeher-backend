<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\OpportunityType;
use App\Services\OpportunityService;
use Faker\Factory;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(OpportunityService $service): void
    {
        $faker = Factory::create();

        $businessTypeIds = BusinessType::query()
            ->select('id')
            ->get()
            ->pluck('id');

        $opportunityTypeIds = OpportunityType::query()
            ->select('id')
            ->get()
            ->pluck('id');

        $total = mt_rand(35, 60);

        for ($i = 0; $i < $total; $i++) {
            echo "    - [$i/$total] Creating opportunity ...\n";
            $service->create(
                createdBy: 1,
                businessTypeId: $businessTypeIds->random(),
                opportunityTypeId: $opportunityTypeIds->random(),
                name: $faker->company,
                lowerAmount: mt_rand(10_000, 50_000),
                upperAmount: mt_rand(55_000, 99_000),
                overview: $faker->paragraph(),
                applicationUrl: $faker->url,
                closingAt: $faker->date(),
            );
        }
    }
}
