<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\TestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {email? : Email de l\'utilisateur} {--all : Envoyer à tous les utilisateurs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer une notification de test à un utilisateur ou à tous les utilisateurs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $users = User::all();
            $count = $users->count();
            
            if ($this->confirm("Êtes-vous sûr de vouloir envoyer une notification à {$count} utilisateurs ?")) {
                $bar = $this->output->createProgressBar($count);
                
                $users->each(function ($user) use ($bar) {
                    $user->notify(new TestNotification());
                    $bar->advance();
                });
                
                $bar->finish();
                $this->newLine(2);
                $this->info("Notification de test envoyée à {$count} utilisateurs");
            }
        } else {
            $email = $this->argument('email') ?? $this->ask('Veuillez entrer l\'email de l\'utilisateur');
            
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("Aucun utilisateur trouvé avec l'email: {$email}");
                return 1;
            }
            
            $user->notify(new TestNotification());
            $this->info("Notification de test envoyée à {$user->email}");
        }
        
        return 0;
    }
}
