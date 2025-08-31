<?php

namespace App\Http\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;

class DeleteUser extends Component
{
    public $userId;

    protected $listeners = ['deleteUser' => 'delete'];

    public function delete($event)
    {
        try {
            $user = User::findOrFail($event['userId']);
            
            // Empêche la suppression de l'utilisateur connecté
            if ($user->id === auth()->id()) {
                $this->dispatch('userDeleted', [
                    'type' => 'error',
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
                ]);
                return;
            }

            $user->delete();
            
            $this->dispatch('userDeleted', [
                'type' => 'success',
                'message' => 'Utilisateur supprimé avec succès.'
            ]);

            // Rafraîchir la liste des utilisateurs
            $this->dispatch('userListUpdated');
            
        } catch (\Exception $e) {
            $this->dispatch('userDeleted', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression de l\'utilisateur.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.users.delete-user');
    }
}
