<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $tableMappings = [
            ['oracle' => 'PORTAL.ENTRY_CLAWBACK', 'mysql' => 'entry_clawback'],

        ];

        foreach ($tableMappings as $table) {
            $schedule->command("transfer:data {$table['oracle']} {$table['mysql']}")->everyFiveMinutes();
        }

        $schedule->command('transfer:entries')
            ->everyFiveMinutes()
            ->appendOutputTo(storage_path('logs/transfer_entries.log'));

    }


    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
