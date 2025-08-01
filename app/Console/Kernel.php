<?php

namespace App\Console;

use App\Console\Commands\ImportLocations;
use App\Console\Commands\ReEncodeCustomFieldNames;
use App\Console\Commands\RestoreDeletedUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
{
    $schedule->command('snipeit:inventory-alerts')->daily();
    $schedule->command('snipeit:expiring-alerts')->daily();
    $schedule->command('snipeit:expected-checkin')->daily();
    $schedule->command('snipeit:backup')->weekly();
    $schedule->command('backup:clean')->daily();
    $schedule->command('snipeit:upcoming-audits')->daily();
    $schedule->command('auth:clear-resets')->everyFifteenMinutes();
    $schedule->command('saml:clear_expired_nonces')->weekly();

    // Atualização dos responsáveis
    $schedule->command('responsaveis:atualizar-estados')->twiceDaily(6, 15);

    // Recolha automática de utentes com modos distintos
    $schedule->command('utentes:reset --modo=10h')->dailyAt('10:00');
    $schedule->command('utentes:reset --modo=21h')->dailyAt('21:00');
    $schedule->command('apelido:atualizar')->hourly()->between('8:00', '18:00');

}

    /**
     * This method is required by Laravel to handle any console routes
     * that are defined in routes/console.php.
     */
    protected function commands()
    {
        require base_path('routes/console.php');
        $this->load(__DIR__.'/Commands');
    }


}
