<?php

namespace App\Events;

use App\Models\Absence;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsenceCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * L'absence qui a été créée
     *
     * @var \App\Models\Absence
     */
    public $absence;

    /**
     * Crée une nouvelle instance de l'événement.
     *
     * @param \App\Models\Absence $absence
     * @return void
     */
    public function __construct(Absence $absence)
    {
        $this->absence = $absence->load(['eleve', 'professeur', 'matiere']);
    }
}
