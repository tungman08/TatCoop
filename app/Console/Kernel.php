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
        Commands\AdminCommand::class,
        Commands\BingCommand::class,
        Commands\MailCommand::class,
        Commands\ViewMakeCommand::class,
        Commands\DividendCommand::class,
        Commands\PaymentCalculateCommand::class,
        Commands\PaymentStoreCommand::class,
        Commands\ShareholdingCalculateCommand::class,
        Commands\ShareholdingStoreCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('bing')->dailyAt('03:00');
    }
}
