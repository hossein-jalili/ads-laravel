<?php

namespace App\Console;


namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     *
     * @var array
     */
    protected $commands = [
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:xentral-shipping-methods')
            ->everyThreeHours()
            ->withoutOverlapping()
            ->appendOutputTo('logs/xentral-shipping-methods-sync.log');
        $schedule->command('sync:xentral-products')
            ->everyThreeHours()
            ->withoutOverlapping()
            ->appendOutputTo('logs/xentral-products-sync.log');
    }

    /**
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
