<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price_label' => 'TZS 0',
                'price' => 0,
                'max_products' => 50,
                'max_staff' => 2,
                'max_shops' => 1,
                'has_reports' => true,
                'has_exports' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price_label' => 'TZS 25,000/mo',
                'price' => 25000,
                'max_products' => 500,
                'max_staff' => 10,
                'max_shops' => 3,
                'has_reports' => true,
                'has_exports' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'price_label' => 'TZS 75,000/mo',
                'price' => 75000,
                'max_products' => null, // unlimited
                'max_staff' => null,
                'max_shops' => null,
                'has_reports' => true,
                'has_exports' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}