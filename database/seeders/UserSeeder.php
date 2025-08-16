<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\BusinessStage;
use App\Models\Industry;
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
        $faker = Factory::create()->unique();

        $country = GeoCountry::query()
            ->whereRaw('LOWER(name) = ?', ['nigeria'])
            ->first();

        $industries = Industry::query()
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
        $businessTypeIds = fn() => array_map(fn(int $i) => $industries[$i]['id'], array_rand($industries, mt_rand(2, 4)));
        $businessStageIds = fn() => array_map(fn(int $i) => $stages[$i]['id'], array_rand($stages, mt_rand(2, 4)));
        $opportunityTypeIds = fn() => array_map(fn(int $i) => $opTypes[$i]['id'], array_rand($opTypes, mt_rand(2, 4)));

        $service->create(
            invitedBy: null,
            industryId: null,
            businessName: 'Financeher',
            firstName: 'Super',
            lastName: 'Admin',
            email: 'super.admin@financeher.co',
            rawPassword: self::SEED_PASSWORD,
            mobileNumber: '07011223344',
            isAdmin: false,
            role: UserRole::SUPER_ADMIN,
        );

        $service->create(
            invitedBy: null,
            industryId: null,
            businessName: 'SpiralOver',
            firstName: 'Ahmad',
            lastName: 'Mustapha',
            email: 'me@ahmard.com',
            rawPassword: self::SEED_PASSWORD,
            mobileNumber: '07035636394',
            isAdmin: true,
            role: UserRole::SUPER_ADMIN,
        );

        for ($i = 0; $i < mt_rand(10, 30); $i++) {
            echo "    -> [$i] Seeding user ... \n";

            $service->create(
                invitedBy: null,
                industryId: null,
                businessName: null,
                firstName: $faker->firstName(),
                lastName: $faker->lastName(),
                email: $faker->email(),
                rawPassword: self::SEED_PASSWORD,
                mobileNumber: $faker->phoneNumber(),
                isAdmin: true,
                role: UserRole::random(),
            );
        }
    }
}
