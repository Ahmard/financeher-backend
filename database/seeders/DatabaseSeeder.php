<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        define('IS_MIGRATING', true);

        $this->call(GeoDataSeeder::class);
        $this->call(BusinessTypeSeeder::class);
        $this->call(BusinessStageSeeder::class);
        $this->call(OpportunityTypeSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(WalletSeeder::class);
        $this->call(SystemSettingSeeder::class);
    }
}
