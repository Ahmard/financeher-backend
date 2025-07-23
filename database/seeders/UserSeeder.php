<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\BusinessStage;
use App\Models\BusinessType;
use App\Models\GeoCountry;
use App\Models\OpportunityType;
use App\Services\UserService;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    const string SEED_PASSWORD = 'Pass.1234';

    /**
     * Run the database seeds.
     */
    public function run(UserService $service): void
    {
        $faker = Factory::create();

        $country = GeoCountry::query()
            ->whereRaw('LOWER(name) = ?', ['nigeria'])
            ->first();

        $types = BusinessType::query()
            ->select('id')
            ->get()
            ->toArray();

        $stages = BusinessStage::query()
            ->select('id')
            ->get()
            ->toArray();

        $opTypes = OpportunityType::query()
            ->select('id')
            ->get()
            ->toArray();

        // pick random values from types
        $businessTypeIds = array_rand($types, mt_rand(3, 6));

        $service->create(
            invitedBy: null,
            countryId: $country['id'],
            businessTypeIds: array_rand($types, mt_rand(2, 4)),
            businessStageIds: array_rand($stages, mt_rand(2, 4)),
            opportunityTypeIds: array_rand($opTypes, mt_rand(2, 4)),
            businessName: 'Financeher',
            firstName: 'Super',
            lastName: 'Admin',
            email: 'super.admin@example.com',
            rawPassword: self::SEED_PASSWORD,
            mobileNumber: '07011223344',
            role: UserRole::SUPER_ADMIN,
        );

        $service->create(
            invitedBy: null,
            countryId: $country['id'],
            businessTypeIds: array_rand($types, mt_rand(1, 3)),
            businessStageIds: array_rand($stages, mt_rand(1, 3)),
            opportunityTypeIds: array_rand($opTypes, mt_rand(1, 3)),
            businessName: 'SpiralOver',
            firstName: 'Ahmad',
            lastName: 'Mustapha',
            email: 'me@ahmard.com',
            rawPassword: self::SEED_PASSWORD,
            mobileNumber: '07035636394',
            role: UserRole::SUPER_ADMIN,
        );

        for ($i = 0; $i < mt_rand(10, 30); $i++) {
            echo "    -> [$i] Seeding user ... \n";

            $service->create(
                invitedBy: null,
                countryId: $country['id'],
                businessTypeIds: array_rand($types, mt_rand(1, 3)),
                businessStageIds: array_rand($stages, mt_rand(1, 3)),
                opportunityTypeIds: array_rand($opTypes, mt_rand(1, 3)),
                businessName: null,
                firstName: $faker->firstName(),
                lastName: $faker->lastName(),
                email: $faker->email(),
                rawPassword: self::SEED_PASSWORD,
                mobileNumber: $faker->phoneNumber(),
                role: UserRole::random(),
            );
        }
    }
}
