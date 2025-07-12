<?php

namespace Database\Seeders;

use App\Models\SimAd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SimAdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SimAd::create([
            'owner_name' => 'علی محمدی',
            'number' => '09121234567',
            'price_suggestion' => 25000000,
            'city' => 'تهران',
            'type' => 'custom_offer',
        ]);

        SimAd::create([
            'owner_name' => 'حسین حسینی',
            'number' => '09121234568',
            'price_suggestion' => 20000000,
            'city' => 'مشهد',
            'type' => 'instant_sale',
        ]);

        SimAd::create([
            'owner_name' => 'مریم احمدی',
            'number' => '09121234569',
            'price_suggestion' => 15000000,
            'city' => 'اصفهان',
            'type' => 'custom_offer',
        ]);
    }
}
