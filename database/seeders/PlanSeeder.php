<?php

namespace Database\Seeders;

use App\Enums\Types\BillingCycleKind;
use App\Services\PlanService;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PlanService $service): void
    {
        $service->create(
            createdBy: 1,
            name: 'Financeher Rhodium',
            price: mt_rand(70, 80),
            features: [
                'Access to 1,000+ opportunities in our database',
                '1 Live Funding Webinar',
                'AI Matched Opportunities',
                'Al Funding Assistant',
                'Monthly AMA - Ask me anything with Financeher Mentors',
                'Search the database for relevant opportunities',
                'Advanced filters to refine your search',
            ],
            billingCycle: BillingCycleKind::MONTHLY
        );

        $service->create(
            createdBy: 1,
            name: 'Financeher Platinum',
            price: mt_rand(40, 60),
            features: [
                'Access to financial dashboard',
                'Monthly budget planning',
                'Email support',
            ],
            billingCycle: BillingCycleKind::MONTHLY
        );

        $service->create(
            createdBy: 1,
            name: 'Financeher Gold',
            price: 499,
            features: [
                'Everything in Silver',
                'Automated financial reports',
                'Priority email support',
                'Multi-user access (up to 3 users)',
                'Custom goal tracking',
            ],
            billingCycle: BillingCycleKind::MONTHLY
        );

    }
}
