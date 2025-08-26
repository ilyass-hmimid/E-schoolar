<?php

namespace Tests\Feature;

use App\Enums\RoleType;
use App\Models\Notification as NotificationModel;
use App\Models\Paiement;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = new NotificationService();
    }

    /** @test */
    public function it_can_create_a_payment_delay_notification()
    {
        // Créer un élève
        $user = User::factory()->create([
            'role' => RoleType::ELEVE
        ]);

        // Créer une notification de retard
        $this->notificationService->notifyPaiementRetard(
            $user,
            5, // jours de retard
            1500.00, // montant dû
            ['Mathématiques', 'Physique'] // matières
        );

        // Vérifier que la notification a été créée
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => NotificationModel::TYPE_PAIEMENT_RETARD,
        ]);
    }

    /** @test */
    public function it_can_create_a_payment_confirmation_notification()
    {
        // Créer un paiement
        $user = User::factory()->create([
            'role' => RoleType::ELEVE
        ]);

        $paiement = Paiement::factory()->create([
            'user_id' => $user->id,
            'montant' => 1500,
            'date_paiement' => now(),
            'statut' => 'paye'
        ]);

        // Créer une notification de confirmation
        $this->notificationService->notifyPaiementEffectue($paiement);

        // Vérifier que la notification a été créée pour l'admin
        $this->assertDatabaseHas('notifications', [
            'type' => NotificationModel::TYPE_PAIEMENT_EFFECTUE,
        ]);
    }
}
