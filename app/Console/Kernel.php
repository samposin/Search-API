<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        \App\Console\Commands\VisionApi::class,
        \App\Console\Commands\CurrencyLayerExchangeRateApi::class,
        \App\Console\Commands\CollectRevenueData::class,
        \App\Console\Commands\UpdatePublisherStats::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
            ->hourly();

        $schedule->command('revenue:collect')
            ->hourly();

        $schedule->command('visionapi:init')
            ->dailyAt('06:55');

        $schedule->command('currencylayerexchangerateapi:init')
            ->dailyAt('13:00');

    }
}
