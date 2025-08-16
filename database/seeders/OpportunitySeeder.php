<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\GeoCountry;
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

        $industryIds = Industry::query()
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

        $organisations = [
            'Financeher',
            'Spiralover',
            'Foxtive',
            'Quarkaxis',
        ];

        for ($i = 0; $i < $total; $i++) {
            $countryId = $countries->random()['id'];

            echo "    - [$i/$total] Creating opportunity ...\n";
            $service->create(
                createdBy: 1,
                countryId: $countryId,
                industryId: $industryIds->random(),
                opportunityTypeId: $opportunityTypeIds->random(),
                name: $faker->sentence(),
                organisation: $organisations[array_rand($organisations)],
                lowerAmount: mt_rand(10_000, 50_000),
                upperAmount: mt_rand(55_000, 99_000),
                overview: $faker->paragraph(),
                applicationUrl: $faker->url,
                closingAt: $faker->date(),
            );
        }
    }
}
