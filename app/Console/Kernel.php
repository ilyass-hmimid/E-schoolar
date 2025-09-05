<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérification quotidienne des retards de paiement à 9h du matin
        $schedule->command('paiements:check-retards')
                ->dailyAt('09:00')
                ->onSuccess(function () {
                    Log::info('Vérification des retards de paiement exécutée avec succès');
                })
                ->onFailure(function () {
                    Log::error('Échec de la vérification des retards de paiement');
                });
                
        // Calcul mensuel des paiements des professeurs le 1er de chaque mois à 1h du matin
        $schedule->command('payments:calculate --month=now')
                ->monthlyOn(1, '01:00')
                ->onSuccess(function () {
                    Log::info('Calcul des paiements des professeurs effectué avec succès');
                })
                ->onFailure(function () {
                    Log::error('Échec du calcul des paiements des professeurs');
                });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
