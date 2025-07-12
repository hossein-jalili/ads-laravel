<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\SimAd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $simAd1 = SimAd::where('number', '09121234567')->first();
        $simAd2 = SimAd::where('number', '09121234568')->first();


        Offer::create([
            'sim_ad_id' => $simAd1->id,
            'bidder_name' => 'رضا احمدی',
            'price' => 26000000,
        ]);

        Offer::create([
            'sim_ad_id' => $simAd1->id,
            'bidder_name' => 'فرزاد کریمی',
            'price' => 27000000,
        ]);

        Offer::create([
            'sim_ad_id' => $simAd2->id,
            'bidder_name' => 'زهرا رحیمی',
            'price' => 22000000,
        ]);
    }
}
