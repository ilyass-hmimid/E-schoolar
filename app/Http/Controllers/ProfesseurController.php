<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Professeur;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Matiere;
use App\Models\Valeurs_Salaires;
use App\Models\Enseignement;





use Illuminate\Http\Request;


class ProfesseurController extends Controller
{



    public function index()
    {
        $users = Professeur::latest()->get()->map(function ($user){


            $niveaux = $user->niveaux()->pluck('Nom')->toArray();
            $filieres = $user->filieres()->pluck('Intitule')->toArray();
            $matieres = $user->matieres()->pluck('Libelle')->toArray();


            return [
                'id' => $user->id,
                'Nom' => $user->Nom,
                'Prenom' => $user->Prenom,
                'Tele' => $user->Tele,
                'Adresse' => $user->Adresse,
                'Niveaux' => $niveaux,
                'Filieres' => $filieres,
                'Matieres' => $matieres,
                'Date_debut' => $user->Date_debut ? $user->Date_debut : '',
                'created_at' => $user->created_at ? $user->created_at->format(config('app.date_format')) : null,
            ];
        });

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function getEnseignements()
    {
        $users = Enseignement::latest()->get()->map(function ($user){



            $niveau = Niveau::find($user->IdNiv);
            $filiere = Filiere::find($user->IdFil);
            $matiere = Matiere::find($user->IdMat);
            $prof = Professeur::find($user->IdProf);





            return [
                'id' => $user->id,
                'Nom' => $prof->Nom,
                'Prenom' => $prof->Prenom,
                'IdNiv' => $niveau->Nom,
                'IdFil' => $filiere->Intitule,
                'IdMat' => $matiere->Libelle,
                'NbrEtu' => $user->NbrEtu,
                'SalaireParEtu' => $user->SalaireParEtu,
                'Somme' => $user->Somme,
                'Date_debut' => $user->Date_debut ? $user->Date_debut : '',
            ];
        });

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }




    public function store()
    {

        request()->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'niv' => 'required',
            'fil' => 'required',
            'mat' => 'required', // Assurez-vous que 'matieres' est un tableau
            'Date_debut' => 'required', // Assurez-vous que 'matieres' est un tableau

        ]);

        // Récupération de l'ID du niveau (IdNiv) depuis la table Niveau
        $niveau = Niveau::where('Nom', request('niv'))->first();
        $IdNiv = $niveau ? $niveau->id : null; // ID du niveau ou null si non trouvé

        // Récupération de l'ID de la filière (IdFil) depuis la table Filiere
        $filiere = Filiere::where('Intitule', request('fil'))->first();
        $IdFil = $filiere ? $filiere->id : null; // ID de la filière ou null si non trouvée

         // Récupération de l'ID de la filière (IdFil) depuis la table Filiere
         $matiere = Matiere::where('Libelle', request('mat'))->first();
         $IdMat = $matiere ? $matiere->id : null; // ID de la filière ou null si non trouvée

        $dateDebut = request('Date_debut') . '-01'; // Ajoutez le jour 01 pour former une date complète au format YYYY-MM-DD
        // dd($dateDebut);


        // Création de l'étudiant avec les IDs récupérés
        $professeur = Professeur::create([
            'Nom' => request('nom'),
            'Prenom' => request('prenom'),
            'Tele' => request('tele'),
            'Adresse' => request('adresse'),
            'Date_debut' => $dateDebut,
        ]);



            $val_salaire = Enseignement::where('IdProf',$professeur->id)->get();
            $totalSalire=0;
            foreach($val_salaire as $val){
                $totalSalire=$totalSalire+$val->Somme;

            }
            $professeur->update([
                'SommeApaye' =>  $totalSalire,
            ]);


        $SalaireParEtu = Valeurs_Salaires::where('IdNiv', $IdNiv)->where('IdMat', $IdMat)->first();


        $ensiegnement = Enseignement::create([
            'IdProf' => $professeur->id,
            'IdNiv' => $IdNiv,
            'IdFil' => $IdFil,
            'IdMat' => $IdMat,
            'SalaireParEtu' => $SalaireParEtu->Valeur,
            'Date_debut' => $dateDebut,
        ]);





        return $this->index(request());
    }

    public function getProfs(){
        // $profs = Professeur::pluck('Prenom', 'Nom', 'id'); // Récupérer les données des colonnes 'Prenom', 'Nom' avec l'ID
        $profs = Professeur::all();

// dd($profs);
        return $profs;
    }




    public function createEnseignement()
    {

        request()->validate([
            'currentProf' => 'required',
            'niv' => 'required',
            'fil' => 'required',
            'mat' => 'required', // Assurez-vous que 'matieres' est un tableau
            'Date_debut' => 'required', // Assurez-vous que 'matieres' est un tableau

        ]);

        // Récupération de l'ID du niveau (IdNiv) depuis la table Niveau
        $niveau = Niveau::where('Nom', request('niv'))->first();
        $IdNiv = $niveau ? $niveau->id : null; // ID du niveau ou null si non trouvé

        // Récupération de l'ID de la filière (IdFil) depuis la table Filiere
        $filiere = Filiere::where('Intitule', request('fil'))->first();
        $IdFil = $filiere ? $filiere->id : null; // ID de la filière ou null si non trouvée

         // Récupération de l'ID de la filière (IdFil) depuis la table Filiere
         $matiere = Matiere::where('Libelle', request('mat'))->first();
         $IdMat = $matiere ? $matiere->id : null; // ID de la filière ou null si non trouvée

         $prof = Professeur::where('Nom',request('currentProf')['Nom'])->where('Prenom',request('currentProf')['Prenom'])->first();



        $dateDebut = request('Date_debut') . '-01'; // Ajoutez le jour 01 pour former une date complète au format YYYY-MM-DD
        // dd($dateDebut);




        $SalaireParEtu = Valeurs_Salaires::where('IdNiv', $IdNiv)->where('IdMat', $IdMat)->first();


        $ensiegnement = Enseignement::create([
            'IdProf' => $prof->id,
            'IdNiv' => $IdNiv,
            'IdFil' => $IdFil,
            'IdMat' => $IdMat,
            'SalaireParEtu' => $SalaireParEtu->Valeur,
            'Date_debut' => $dateDebut,
        ]);





        return $this->getEnseignements(request());
    }


    public function update(Professeur $user)
    {
        request()->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'Date_debut' => 'required',
        ]);
        $dateDebut = request('Date_debut') . '-01'; // Ajoutez le jour 01 pour former une date complète au format YYYY-MM-DD

        $user->update([
            'Nom' => request('nom'),
            'Prenom' => request('prenom'),
            'Tele' => request('tele'),
            'Adresse' => request('adresse'),
            'Date_debut' =>  $dateDebut,

        ]);
        return $this->index(request());
    }




    public function updateEnseignement(Enseignement $user)
    {
        request()->validate([
            'niv' => 'required',
            'fil' => 'required',
            'mat' => 'required',
            'SalaireParEtu' => 'required',
            'Date_debut' => 'required',
        ]);


        $niveau = Niveau::where('Nom',request('niv'))->first();
        $filiere = Filiere::where('Intitule',request('fil'))->first();
        $matiere = Matiere::where('Libelle',request('mat'))->first();

        $dateDebut = request('Date_debut') . '-01'; // Ajoutez le jour 01 pour former une date complète au format YYYY-MM-DD

        $user->update([
            'IdNiv' => $niveau->id,
            'IdFil' => $filiere->id,
            'IdMat' => $matiere->id,
            'SalaireParEtu' => request('SalaireParEtu'),
            'Date_debut' =>  $dateDebut,

        ]);
        return $this->getEnseignements(request());
    }


    public function destory(Professeur $user)
    {
        $user->delete();

        return response()->noContent();
    }

    public function destoryEnseignement(Enseignement $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
