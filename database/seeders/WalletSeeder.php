<?php

namespace Database\Seeders;

use App\Services\WalletService;
use Faker\Factory;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(WalletService $service): void
    {
        $faker = Factory::create();
        $wallets = $service->repository()->all();

        foreach ($wallets as $wallet) {
            $count = mt_rand(30, 55);

            echo "     - [{$wallet['id']}] seeding $count wallet transactions ... \n";

            for ($i = 0; $i < $count; $i++) {
                $isCreditOperation = !($i > 5 && mt_rand(0, 1) == 0);

                if ($isCreditOperation) {
                    $service->credit(
                        userId: $wallet['user_id'],
                        amount: mt_rand(1000, 5000),
                        narration: $faker->sentence,
                    );
                } else {
                    $service->debit(
                        userId: $wallet['user_id'],
                        amount: mt_rand(1000, 5000),
                        narration: $faker->sentence,
                        throwOnInsufficientBalance: false,
                    );
                }
            }
        }
    }
}
