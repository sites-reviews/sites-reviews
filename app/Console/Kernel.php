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

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('site:update_page_waiting')
            ->everyMinute()
            ->withoutOverlapping(2);

        $schedule->command('site:update_preview_waiting')
            ->everyMinute()
            ->withoutOverlapping(2);

        $schedule->command('sitemap:create')
            ->daily();

        $schedule->command('site:possible_handle')
            ->everyMinute()
            ->withoutOverlapping(5);
/*
        $schedule->command('site_page:search_for_new_domains')
            ->everyMinute()
            ->withoutOverlapping(10);
        */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
