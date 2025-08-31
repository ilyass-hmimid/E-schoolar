<?php

namespace App\Http\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Enums\RoleType;

class ShowUser extends Component
{
    public $userId;
    public $user;
    public $confirmingUserDeletion = false;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->loadUser();
    }

    public function loadUser()
    {
        $this->user = User::with(['niveau', 'filiere', 'classes', 'matieres'])
            ->findOrFail($this->userId);
    }

    public function confirmUserDeletion()
    {
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        try {
            // Empêcher la suppression de son propre compte
            if ($this->user->id === auth()->id()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
                ]);
                return;
            }

            // Vérifier s'il s'agit du dernier administrateur
            if ($this->user->role === RoleType::ADMIN->value) {
                $adminCount = User::where('role', RoleType::ADMIN->value)->count();
                if ($adminCount <= 1) {
                    $this->dispatch('notify', [
                        'type' => 'error',
                        'message' => 'Impossible de supprimer le dernier administrateur.'
                    ]);
                    return;
                }
            }

            $this->user->delete();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Utilisateur supprimé avec succès.'
            ]);

            return redirect()->route('admin.users.index');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de l\'utilisateur', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression de l\'utilisateur.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.users.show-user');
    }
}
