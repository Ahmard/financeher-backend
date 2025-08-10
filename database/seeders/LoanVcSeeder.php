<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\GeoCountry;
use App\Models\OpportunityType;
use App\Services\LoanVcService;
use Faker\Factory;
use Illuminate\Database\Seeder;

class LoanVcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(LoanVcService $service): void
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

        $countries = GeoCountry::query()
            ->select('id')
            ->get();

        $total = mt_rand(35, 60);

        for ($i = 0; $i < $total; $i++) {
            $countryIds = $countries->random(mt_rand(1, 7))
                ->map(fn(GeoCountry $country) => $country['id'])
                ->toArray();

            echo "    - [$i/$total] Creating loan/vc ...\n";
            $service->create(
                createdBy: 1,
                countryIds: $countryIds,
                businessTypeId: $businessTypeIds->random(),
                opportunityTypeId: $opportunityTypeIds->random(),
                organisation: $faker->company,
                lowerAmount: mt_rand(10_000, 50_000),
                upperAmount: mt_rand(55_000, 99_000),
                description: $faker->paragraph(),
                applicationUrl: $faker->url,
                closingAt: $faker->date(),
            );
        }
    }
}
