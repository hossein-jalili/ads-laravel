<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

            Ad::create([
                'title' => 'تبلیغ اپراتور MCI',
                'description' => 'این یک تبلیغ برای اپراتور MCI است.',
                'operator' => 'mci',
            ]);

            Ad::create([
                'title' => 'تبلیغ اپراتور Irancell',
                'description' => 'این یک تبلیغ برای اپراتور Irancell است.',
                'operator' => 'irancell',
            ]);

            Ad::create([
                'title' => 'تبلیغ اپراتور Rightel',
                'description' => 'این یک تبلیغ برای اپراتور Rightel است.',
                'operator' => 'rightel',
            ]);
    }
}
