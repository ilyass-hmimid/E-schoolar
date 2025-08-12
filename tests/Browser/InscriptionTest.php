<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Classe;
use App\Models\Pack;
use App\Models\Matiere;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InscriptionTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test du processus complet d'inscription d'un nouvel étudiant
     * 
     * @return void
     */
    public function test_complete_student_registration_flow()
    {
        // Créer des données de test
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $classe = Classe::factory()->create([
            'Libelle' => 'Classe de test',
            'Niveau' => '1ère année',
            'Capacite' => 30
        ]);

        $matiere = Matiere::factory()->create([
            'Libelle' => 'Mathématiques',
            'Description' => 'Cours de mathématiques avancées',
            'Coefficient' => 3
        ]);

        $pack = Pack::factory()->create([
            'Libelle' => 'Pack Découverte',
            'Description' => 'Pack de découverte avec 10 heures de cours',
            'Prix' => 500,
            'Duree' => 30, // jours
            'Heures' => 10,
            'Actif' => true
        ]);

        // Associer la matière au pack
        $pack->matieres()->attach($matiere->id);

        $this->browse(function (Browser $browser) use ($admin, $classe, $pack) {
            // Étape 1: Connexion en tant qu'admin
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password')
                ->press('Se connecter')
                ->assertPathIs('/dashboard')
                ->assertSee('Tableau de bord');

            // Étape 2: Accéder à la page de création d'étudiant
            $browser->visit('/etudiants/create')
                ->assertSee('Nouvel Étudiant');

            // Remplir le formulaire d'inscription
            $browser->type('nom', 'Dupont')
                ->type('prenom', 'Jean')
                ->type('email', 'jean.dupont@example.com')
                ->type('telephone', '0612345678')
                ->type('adresse', '123 Rue de Test')
                ->type('code_postal', '75000')
                ->type('ville', 'Paris')
                ->select('classe_id', $classe->id)
                ->select('pack_id', $pack->id)
                ->type('date_naissance', '2000-01-01')
                ->type('lieu_naissance', 'Paris')
                ->type('nom_pere', 'Pierre Dupont')
                ->type('nom_mere', 'Marie Dupont')
                ->type('telephone_urgence', '0612345679')
                ->type('remarques', 'Aucune remarque')
                ->press('Enregistrer')
                ->assertPathIs('/etudiants')
                ->assertSee('L\'étudiant a été créé avec succès');

            // Vérifier que l'étudiant a bien été créé
            $browser->assertSee('Dupont Jean')
                ->assertSee($classe->Libelle);

            // Vérifier que l'inscription a bien été créée
            $this->assertDatabaseHas('inscriptions', [
                'IdEtu' => 1, // L'ID de l'étudiant créé
                'IdPack' => $pack->id,
                'IdClasse' => $classe->id,
                'statut' => 'actif'
            ]);

            // Se déconnecter
            $browser->press('Déconnexion')
                ->assertPathIs('/');
        });
    }

    /**
     * Test de validation du formulaire d'inscription
     * 
     * @return void
     */
    public function test_registration_form_validation()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/etudiants/create')
                ->press('Enregistrer')
                ->assertSee('Le champ nom est obligatoire')
                ->assertSee('Le champ prénom est obligatoire')
                ->assertSee('Le champ email est obligatoire')
                ->assertSee('Le champ téléphone est obligatoire')
                ->assertSee('Le champ date de naissance est obligatoire')
                ->assertSee('Le champ classe est obligatoire');
        });
    }

    /**
     * Test de la recherche d'étudiants
     * 
     * @return void
     */
    public function test_student_search()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $classe = Classe::factory()->create(['Libelle' => 'Classe A']);
        
        // Créer plusieurs étudiants
        $etudiants = \App\Models\Etudiant::factory()->count(3)->create([
            'IdClasse' => $classe->id,
            'Nom' => 'Test',
            'Prenom' => ['Jean', 'Pierre', 'Paul'][$this->faker->unique()->numberBetween(0, 2)]
        ]);

        $this->browse(function (Browser $browser) use ($admin, $etudiants) {
            $browser->loginAs($admin)
                ->visit('/etudiants')
                ->type('search', 'Jean')
                ->press('Rechercher')
                ->assertSee('Jean')
                ->assertDontSee('Pierre')
                ->assertDontSee('Paul');
        });
    }

    /**
     * Test de la modification d'un étudiant
     * 
     * @return void
     */
    public function test_edit_student()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $classe = Classe::factory()->create(['Libelle' => 'Classe A']);
        $nouvelleClasse = Classe::factory()->create(['Libelle' => 'Classe B']);
        
        $etudiant = \App\Models\Etudiant::factory()->create([
            'IdClasse' => $classe->id,
            'Nom' => 'Dupont',
            'Prenom' => 'Jean',
            'Email' => 'jean.dupont@example.com'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $etudiant, $nouvelleClasse) {
            $browser->loginAs($admin)
                ->visit("/etudiants/{$etudiant->id}/edit")
                ->type('nom', 'Durand')
                ->type('prenom', 'Pierre')
                ->type('email', 'pierre.durand@example.com')
                ->select('classe_id', $nouvelleClasse->id)
                ->press('Mettre à jour')
                ->assertPathIs('/etudiants')
                ->assertSee('L\'étudiant a été mis à jour avec succès')
                ->assertSee('Durand Pierre')
                ->assertSee($nouvelleClasse->Libelle);

            // Vérifier que les modifications ont été enregistrées en base de données
            $this->assertDatabaseHas('Etudiant', [
                'id' => $etudiant->id,
                'Nom' => 'Durand',
                'Prenom' => 'Pierre',
                'Email' => 'pierre.durand@example.com',
                'IdClasse' => $nouvelleClasse->id
            ]);
        });
    }

    /**
     * Test de la suppression d'un étudiant
     * 
     * @return void
     */
    public function test_delete_student()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $etudiant = \App\Models\Etudiant::factory()->create([
            'Nom' => 'A Supprimer',
            'Prenom' => 'Test'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $etudiant) {
            $browser->loginAs($admin)
                ->visit('/etudiants')
                ->click('@delete-student-' . $etudiant->id)
                ->whenAvailable('.modal', function ($modal) {
                    $modal->press('Supprimer');
                })
                ->assertSee('L\'étudiant a été supprimé avec succès')
                ->assertDontSee('A Supprimer');

            // Vérifier que l'étudiant a bien été supprimé de la base de données
            $this->assertDatabaseMissing('Etudiant', ['id' => $etudiant->id]);
        });
    }

    /**
     * Test de la vue détaillée d'un étudiant
     * 
     * @return void
     */
    public function test_view_student_details()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $classe = Classe::factory()->create(['Libelle' => 'Classe A']);
        
        $etudiant = \App\Models\Etudiant::factory()->create([
            'IdClasse' => $classe->id,
            'Nom' => 'Dupont',
            'Prenom' => 'Jean',
            'Email' => 'jean.dupont@example.com',
            'Telephone' => '0612345678',
            'DateNaissance' => '2000-01-01',
            'LieuNaissance' => 'Paris',
            'Adresse' => '123 Rue de Test',
            'CodePostal' => '75000',
            'Ville' => 'Paris',
            'NomPere' => 'Pierre Dupont',
            'NomMere' => 'Marie Dupont',
            'TelephoneUrgence' => '0612345679',
            'Remarques' => 'Aucune remarque'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $etudiant, $classe) {
            $browser->loginAs($admin)
                ->visit("/etudiants/{$etudiant->id}")
                ->assertSee('Dupont Jean')
                ->assertSee($classe->Libelle)
                ->assertSee('jean.dupont@example.com')
                ->assertSee('0612345678')
                ->assertSee('01/01/2000')
                ->assertSee('Paris')
                ->assertSee('123 Rue de Test')
                ->assertSee('75000')
                ->assertSee('Pierre Dupont')
                ->assertSee('Marie Dupont')
                ->assertSee('0612345679')
                ->assertSee('Aucune remarque');
        });
    }
}
