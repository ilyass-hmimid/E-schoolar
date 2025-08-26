<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckRetardsPaiement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paiements:check-retards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les retards de paiement et envoie des notifications si nécessaire';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Début de la vérification des retards de paiement...');
        
        try {
            $notificationService->checkRetardsPaiement();
            $this->info('Vérification des retards de paiement terminée avec succès.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erreur lors de la vérification des retards: ' . $e->getMessage());
            Log::error('Erreur lors de la vérification des retards de paiement: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
