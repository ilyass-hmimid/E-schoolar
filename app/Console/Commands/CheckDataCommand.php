<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les données de la base de données';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Vérification des données de la base de données ===');
        
        // Vérifier les filières
        $this->checkFilieres();
        
        // Vérifier les niveaux
        $this->checkNiveaux();
        
        // Vérifier les classes
        $this->checkClasses();
        
        // Vérifier les cours
        $this->checkCours();
        
        // Vérifier les étudiants
        $this->checkEtudiants();
    }
    
    private function checkFilieres()
    {
        $this->info('\n=== Filières ===');
        $filieres = DB::table('filieres')->select('id', 'nom as libelle', 'code', 'niveau_id')->get();
        
        if ($filieres->isEmpty()) {
            $this->error('Aucune filière trouvée dans la base de données.');
            return;
        }
        
        $this->table(
            ['ID', 'Nom', 'Code', 'Niveau ID'],
            $filieres->map(function ($filiere) {
                return [
                    'id' => $filiere->id,
                    'nom' => $filiere->libelle, // On utilise libelle comme alias pour maintenir la cohérence du code
                    'code' => $filiere->code,
                    'niveau_id' => $filiere->niveau_id
                ];
            })
        );
        
        $expectedFilieres = [
            'Sciences Mathématiques',
            'Lettres',
            'Sciences Humaines',
            'Sciences Physiques',
            'Sciences de la Vie et de la Terre',
            'Sciences Économiques'
        ];
        
        $missing = [];
        foreach ($expectedFilieres as $expected) {
            $exists = false;
            foreach ($filieres as $filiere) {
                if (trim($filiere->libelle) === trim($expected)) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $missing[] = $expected;
            }
        }
        
        if (!empty($missing)) {
            $this->warn('Filières manquantes : ' . implode(', ', $missing));
        } else {
            $this->info('Toutes les filières attendues sont présentes.');
        }
    }
    
    private function checkNiveaux()
    {
        $this->info('\n=== Niveaux ===');
        $niveaux = DB::table('niveaux')->select('id', 'nom as libelle', 'code')->get();
        
        if ($niveaux->isEmpty()) {
            $this->error('Aucun niveau trouvé dans la base de données.');
            return;
        }
        
        $this->table(
            ['ID', 'Nom', 'Code'],
            $niveaux->map(function ($niveau) {
                return [
                    'id' => $niveau->id,
                    'nom' => $niveau->libelle, // On utilise libelle comme alias
                    'code' => $niveau->code
                ];
            })
        );
    }
    
    private function checkClasses()
    {
        $this->info('\n=== Classes ===');
        $classes = DB::table('classes')
            ->select(
                'classes.id', 
                'classes.nom as nom_classe',
                'niveaux.nom as niveau_nom',
                'filieres.nom as filiere_nom',
                'classes.professeur_principal_id',
                'classes.annee_scolaire'
            )
            ->leftJoin('niveaux', 'classes.niveau_id', '=', 'niveaux.id')
            ->leftJoin('filieres', 'classes.filiere_id', '=', 'filieres.id')
            ->get();
        
        if ($classes->isEmpty()) {
            $this->warn('Aucune classe trouvée dans la base de données.');
            return;
        }
        
        $this->table(
            ['ID', 'Nom', 'Niveau', 'Filière', 'Prof Principal ID', 'Année Scolaire'],
            $classes->map(function ($classe) {
                return [
                    'id' => $classe->id,
                    'nom' => $classe->nom_classe,
                    'niveau' => $classe->niveau_nom,
                    'filiere' => $classe->filiere_nom,
                    'professeur_principal_id' => $classe->professeur_principal_id,
                    'annee_scolaire' => $classe->annee_scolaire
                ];
            })
        );
    }
    
    private function checkCours()
    {
        $this->info('\n=== Cours ===');
        $cours = DB::table('cours')
            ->select(
                'cours.id',
                'matieres.nom as matiere_nom',
                'classes.nom as classe_nom',
                'users.name as enseignant_nom',
                'cours.date',
                'cours.heure_debut',
                'cours.heure_fin',
                'cours.statut',
                'cours.est_valide'
            )
            ->leftJoin('matieres', 'cours.matiere_id', '=', 'matieres.id')
            ->leftJoin('classes', 'cours.classe_id', '=', 'classes.id')
            ->leftJoin('users', 'cours.enseignant_id', '=', 'users.id')
            ->get();
        
        if ($cours->isEmpty()) {
            $this->warn('Aucun cours trouvé dans la base de données.');
            return;
        }
        
        $this->table(
            ['ID', 'Matière', 'Classe', 'Enseignant', 'Date', 'Heure', 'Statut', 'Validé'],
            $cours->map(function ($cours) {
                return [
                    'id' => $cours->id,
                    'matiere' => $cours->matiere_nom,
                    'classe' => $cours->classe_nom,
                    'enseignant' => $cours->enseignant_nom,
                    'date' => $cours->date,
                    'heure' => substr($cours->heure_debut, 0, 5) . ' - ' . substr($cours->heure_fin, 0, 5),
                    'statut' => $cours->statut,
                    'valide' => $cours->est_valide ? 'Oui' : 'Non'
                ];
            })
        );
    }
    
    private function checkEtudiants()
    {
        $this->info('\n=== Étudiants ===');
        $etudiants = DB::table('etudiants')
            ->select('id', 'nom', 'prenom', 'classe_id', 'user_id')
            ->limit(10)
            ->get();
        
        if ($etudiants->isEmpty()) {
            $this->error('Aucun étudiant trouvé dans la base de données.');
            return;
        }
        
        $this->table(
            ['ID', 'Nom', 'Prénom', 'Classe ID', 'User ID'],
            $etudiants->map(function ($etudiant) {
                return [
                    'id' => $etudiant->id,
                    'nom' => $etudiant->nom,
                    'prenom' => $etudiant->prenom,
                    'classe_id' => $etudiant->classe_id,
                    'user_id' => $etudiant->user_id
                ];
            })
        );
        
        $total = DB::table('etudiants')->count();
        $withClass = DB::table('etudiants')->whereNotNull('classe_id')->count();
        $withoutClass = $total - $withClass;
        
        $this->info("Total étudiants : $total");
        $this->info("Étudiants avec classe : $withClass");
        $this->info("Étudiants sans classe : $withoutClass");
        
        if ($withoutClass > 0) {
            $this->warn("Attention : $withoutClass étudiants n'ont pas de classe attribuée.");
        }
    }
}
