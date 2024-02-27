<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


use App\Models\User;
use App\Models\Etudiant;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Matiere;
use App\Models\Inscription;
use App\Models\Historique_Inscriptions;
use App\Models\Valeurs_Paiments;
use App\Models\Paiment;
use App\Models\Valeurs_Salaires;
use App\Models\Enseignement;
use App\Models\Professeur;
use App\Models\Salaires;
use App\Models\Absence;







use DateTime; // Ajoutez cette ligne au début du fichier pour importer la classe DateTime








use Illuminate\Http\Request;


class CentreController extends Controller
{



    public function getSlaireForProf(Request $request)
    {
//   dd($request->input('id'));
  $prof = Professeur::find($request->input('id'));
//   dd($prof->SommeApaye);
return $prof->SommeApaye;
    }

    public function index()
    {
        $users = Etudiant::latest()->get()->map(function ($user){
            $niveau = Niveau::find($user->IdNiv);
            $filiere = Filiere::find($user->IdFil);
            $matieres = $user->matieres()->pluck('Libelle')->toArray();
            $professeurs = $user->professeurs()->pluck('Nom')->toArray();


            return [
                'id' => $user->id,
                'Nom' => $user->Nom,
                'Prenom' => $user->Prenom,
                'Tele' => $user->Tele,
                'Adresse' => $user->Adresse,
                'SommeApaye' => $user->SommeApaye,
                'IdNiv' => $niveau ? $niveau->Nom : null,
                'IdFil' => $filiere ? $filiere->Intitule : null,
                'Matieres' => $matieres,
                'Professeurs' => $professeurs,
                'Date_debut' => $user->Date_debut ? $user->Date_debut : '',
                'created_at' => $user->created_at ? $user->created_at->format(config('app.date_format')) : null,
            ];
        });

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }



    public function getListeAbsences()
    {
        $users = Absence::where('Status','Absent')->get()->map(function ($user){
            // dd($user);
            $etudiant = Etudiant::find($user->IdEtu);
            $niv= Niveau::find($user->IdNiv);
            $fil=Filiere::find($user->IdFil);
            $mat=Matiere::find($user->IdMat);
            $prof=Professeur::find($user->IdProf);





            return [
                'id' => $user->id,
                'Nom' => $etudiant->Nom,
                'Prenom' => $etudiant->Prenom,
                'Tele' => $etudiant->Tele,
                'Adresse' => $etudiant->Adresse,
                // 'SommeApaye' => $user->SommeApaye,
                'IdNiv' => $niv->Nom,
                'IdFil' => $fil->Intitule,
                'Matieres' => $mat->Libelle,
                'Professeurs' => $prof->Nom .' '. $prof->Prenom,
                'Date_debut' => $user->Date_absence,
                'created_at' => '',
            ];
        });

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }



    public function getEnseignementsParProf(Request $request)
{
    $enseignements = Enseignement::find($request->input('id'))->get();

    return response()->json($enseignements, 200, [], JSON_UNESCAPED_UNICODE);
}




    public function getUsersForProf(Request $request)
    {
        $i=0;
        $SalaireActuelle=0;

        // dd($request->input('id'));
        $datePourAfficher = $request->input('date'); // Récupération du mois sélectionné depuis la requête


        $EtusEnsParProf = Inscription::where('IdProf', $request->input('id'))->get();
        $users = collect(); // Initialiser une collection pour stocker les étudiants
        $mats = collect();

        foreach ($EtusEnsParProf as $etu) {
            $user = Etudiant::find($etu->IdEtu); // Récupérer l'étudiant par son ID dans chaque inscription
            if ($user) {
                $users->push($user); // Ajouter l'étudiant à la collection
                $mats->push($etu->IdMat);
            }
        }

        // dd($users);




        // Initialiser le tableau résultat
        $result = [];

        foreach ($users as $user) {
            // Convertir la date sélectionnée en objet DateTime pour extraire le mois et l'année
            $dateAfficher = new DateTime($datePourAfficher);
            $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
            $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée

            // Vérifier s'il existe un paiement pour cet étudiant et ce mois
            $Paiment = Paiment::where('IdEtu', $user->id)
                ->whereRaw('MONTH(Date_Paiment) = ?', [$mois])
                ->whereRaw('YEAR(Date_Paiment) = ?', [$annee])
                ->first();

            $ens = Enseignement::where('IdProf',$request->input('id'))
                                ->where('IdMat',$mats[$i])
                                ->where('IdNiv',$user->IdNiv)
                                ->where('IdFil',$user->IdFil)
                                ->first();

            $mati = Matiere::find($mats[$i]);
            $nive= Niveau::find($user->IdNiv);
            $fili= Filiere::find($user->IdFil);

            $i=$i+1;

            if($ens && $Paiment && $Paiment->Etat == 'Payé'){
                $SalaireActuelle += $ens->SalaireParEtu;
            }


            $Salaire = Salaires::where('IdProf', $request->input('id'))
            ->whereRaw('MONTH(Date_Salaire) = ?', [$mois])
            ->whereRaw('YEAR(Date_Salaire) = ?', [$annee])
            ->first();


            // if (!$Paiment) {
            //     // Si aucun paiement trouvé, ajouter les détails par défaut
            //     $result[] = [
            //         'id' => $user->id,
            //         'Nom' => $user->Nom,
            //         'Prenom' => $user->Prenom,
            //         'SommeApaye' => $ens->SalaireParEtu,
            //         'Etat' => 'Non payé',
            //         'Montant' => $nive->Nom,
            //         'Reste' => $fili->Intitule,
            //         'Matiere' => $mati->Libelle,
            //         'DatePaiment' => '',
            //         'SalaireActuelle' => $SalaireActuelle,
            //     ];
            // }
            // dd($Salaire);
            if($ens && $Paiment && $Paiment->Etat == 'Payé') {
                // Si un paiement est trouvé, ajouter les détails du paiement
                $result[] = [
                    'id' => $user->id,
                    'Nom' => $user->Nom . ' ' . $user->Prenom,
                    'Prenom' => $user->Prenom,
                    'SommeApaye' => $ens->SalaireParEtu,
                    'Etat' => $Paiment->Etat,
                    'Montant' => $nive->Nom,
                    // 'Montant_actuel' => $Salaire->Montant_actuel !== null ? $Salaire->Montant_actuel : 0,
                    'Reste' => $fili->Intitule,
                    'Matiere' => $mati->Libelle,
                    'DatePaiment' => $Paiment->Date_Paiment,
                    // 'SalaireActuelle' => $SalaireActuelle,
                    'SalaireActuelle' => $Salaire->Montant_actuel !== null ? $Salaire->Montant_actuel : 0,
                ];
            }
        }

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }



    public function getUsersForProfForAbsence(Request $request)
    {
        $i=0;


        // dd($request->input('id'));
        $datePourAfficher = $request->input('date'); // Récupération du mois sélectionné depuis la requête

        // dd($datePourAfficher);


        $EtusEnsParProf = Inscription::where('IdProf', $request->input('id'))->get();
        $users = collect(); // Initialiser une collection pour stocker les étudiants
        $mats = collect();

        foreach ($EtusEnsParProf as $etu) {
            $user = Etudiant::find($etu->IdEtu); // Récupérer l'étudiant par son ID dans chaque inscription
            if ($user) {
                $users->push($user); // Ajouter l'étudiant à la collection
                $mats->push($etu->IdMat);
            }
        }

        // dd($users);




        // Initialiser le tableau résultat
        $result = [];

        foreach ($users as $user) {
            // Convertir la date sélectionnée en objet DateTime pour extraire le mois et l'année
            $dateAfficher = new DateTime($datePourAfficher);
            $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
            $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée

            // dd($mois);

            // Vérifier s'il existe un paiement pour cet étudiant et ce mois
            $Paiment = Absence::where('IdEtu', $user->id)
                    ->where('Date_absence',$datePourAfficher)
                // ->whereRaw('MONTH(Date_absence) = ?', [$mois])
                // ->whereRaw('YEAR(Date_absence) = ?', [$annee])
                ->first();

            $ens = Enseignement::where('IdProf',$request->input('id'))
                                ->where('IdMat',$mats[$i])
                                ->where('IdNiv',$user->IdNiv)
                                ->where('IdFil',$user->IdFil)
                                ->first();
            // dd($user);
            $mati = Matiere::find($mats[$i]);
            $nive= Niveau::where('id',$user->IdNiv)->first();
            $fili= Filiere::where('id',$user->IdFil)->first();

            // dd($nive);

            $i=$i+1;


            if($Paiment && $Paiment->Status == 'Absent') {
                // Si un paiement est trouvé, ajouter les détails du paiement
                $result[] = [
                    'id' => $user->id,
                    'Nom' => $user->Nom . ' ' . $user->Prenom,
                    'Prenom' => $user->Prenom,
                    'Filiere' => $nive->Nom. "\n" .$fili->Intitule. "\n" .$mati->Libelle,
                    // 'SommeApaye' => $ens->SalaireParEtu,
                    'Etat' => $Paiment->Status,
                    // 'Montant' => $nive->Nom,
                    // 'Reste' => $fili->Intitule,
                    // 'Matiere' => $mati->Libelle,
                    'DatePaiment' => $Paiment->Date_absence,
                ];
            }

            else {
                if($nive && $fili && $mati){
                    // dd($nive->Nom);
                    $result[] = [
                        'id' => $user->id,
                        'Nom' => $user->Nom . ' ' . $user->Prenom,
                        'Prenom' => $user->Prenom,
                        'Filiere' => $nive->Nom. "\n" .$fili->Intitule. "\n" .$mati->Libelle,
                        // 'SommeApaye' => $ens->SalaireParEtu,
                        'Etat' => 'Présent',
                        // 'Montant' => $nive->Nom,
                        // 'Reste' => $fili->Intitule,
                        // 'Matiere' => $mati->Libelle,
                        'DatePaiment' => '',
                        // 'SalaireActuelle' => $SalaireActuelle,
                    ];
                }
            }
        }

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }



    public function getUsersForPaiment(Request $request)
    {
        $datePourAfficher = $request->input('date'); // Récupération du mois sélectionné depuis la requête
        // dd($datePourAfficher);

        // Récupérer tous les étudiants avec une Date_debut inférieure ou égale à $datePourAfficher
        $users = Etudiant::whereRaw('DATE_FORMAT(Date_debut, "%Y-%m") <= ?', [$datePourAfficher])->latest()->get();







        // Initialiser le tableau résultat
        $result = [];

        foreach ($users as $user) {
            $niveau = Niveau::find($user->IdNiv);
            $filiere = Filiere::find($user->IdFil);
            $matieres = $user->matieres()->pluck('Libelle')->toArray();
            $professeurs = $user->professeurs()->pluck('Nom')->toArray();
            // Convertir la date sélectionnée en objet DateTime pour extraire le mois et l'année
            $dateAfficher = new DateTime($datePourAfficher);
            $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
            $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée

            // Vérifier s'il existe un paiement pour cet étudiant et ce mois
            $Paiment = Paiment::where('IdEtu', $user->id)
                ->whereRaw('MONTH(Date_Paiment) = ?', [$mois])
                ->whereRaw('YEAR(Date_Paiment) = ?', [$annee])
                ->first();

            if (!$Paiment) {
                // Si aucun paiement trouvé, ajouter les détails par défaut
                $result[] = [
                    'id' => $user->id,
                    'Nom' => $user->Nom,
                    'Prenom' => $user->Prenom,
                    'IdNiv' => $niveau ? $niveau->Nom : null,
                    'IdFil' => $filiere ? $filiere->Intitule : null,
                    'Matieres' => $matieres,
                    'Professeurs' => $professeurs,
                    'SommeApaye' => $user->SommeApaye,
                    'Etat' => 'Non payé',
                    'Montant' => '0',
                    'Reste' => $user->SommeApaye,
                    'DatePaiment' => '',
                ];
            } else {
                // Si un paiement est trouvé, ajouter les détails du paiement
                $result[] = [
                    'id' => $user->id,
                    'Nom' => $user->Nom,
                    'Prenom' => $user->Prenom,
                    'IdNiv' => $niveau ? $niveau->Nom : null,
                    'IdFil' => $filiere ? $filiere->Intitule : null,
                    'Matieres' => $matieres,
                    'Professeurs' => $professeurs,
                    'SommeApaye' => $Paiment->SommeApaye ? $Paiment->SommeApaye : $user->SommeApaye,
                    'Etat' => $Paiment->Etat,
                    'Montant' => $Paiment->Montant,
                    'Reste' => $Paiment->Reste,
                    'DatePaiment' => $Paiment->Date_Paiment,
                ];
            }
        }

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createNewSalaryIfNotExistsForProfs() {
        $professeurs = Professeur::get();
        foreach($professeurs as $professeur) {
            $inscriptions = Inscription::where('IdProf', $professeur->id)->get();
            foreach($inscriptions as $inscription) {
                $enseignements = Enseignement::where('IdProf', $professeur->id)->get();
                $paiments = Paiment::where('IdEtu', $inscription->IdEtu)->get();
                foreach($paiments as $paiment) {
                    if($paiment->Etat == 'Payé' || $paiment->Etat == 'Payé et plus') {
                        $salaire = Salaires::where('IdProf', $professeur->id)
                            ->whereYear('Date_Salaire', date('Y', strtotime($paiment->Date_Paiment)))
                            ->whereMonth('Date_Salaire', date('m', strtotime($paiment->Date_Paiment)))
                            ->first();
                            if(!$salaire) {
                                $salaryMontant = 0;

                                foreach($enseignements as $enseignement) {
                                    $salaryMontant += $enseignement->SalaireParEtu;
                                }
                            
                                Salaires::create([
                                    'Montant' => 0,
                                    'Montant_actuel' => $salaryMontant,
                                    'Reste' => $professeur->SommeApaye,
                                    'Etat' => 'Non payé',
                                    'Date_Salaire' => $paiment->Date_Paiment,
                                    'IdProf' => $professeur->id
                                ]);
                            }
                    }
                }
            }
            
        }
    }

    public function ModificationTotalProvisoir()
    {
        $this->createNewSalaryIfNotExistsForProfs();
        $profs = Professeur::orderBy('created_at', 'desc')->get();

        foreach ($profs as $professeur) {
            // Calcul du total des salaires pour le professeur
            $totalSalire = Enseignement::where('IdProf', $professeur->id)->sum('Somme');

            // Mise à jour du champ 'SommeApaye' du professeur
            $professeur->update([
                'SommeApaye' =>  $totalSalire,
            ]);

            // Récupération des inscriptions du professeur
            $inscriptions = Inscription::where('IdProf', $professeur->id)->get();
            // Récupération des salaires non payés du professeur
            $salaires = Salaires::where('IdProf', $professeur->id)->where('Etat', 'Non payé')->get();

            //recalcule de nombre des étudiants par professeur
            $enseignementsByProf = Enseignement::where('IdProf', $professeur->id)->get();
            foreach($enseignementsByProf as $enseignementItem) {
                $enseignementItem->NbrEtu = 0;
                foreach($inscriptions as $inscriptionItem) {
                    if($enseignementItem->IdFil == $inscriptionItem->IdFil && $enseignementItem->IdMat == $inscriptionItem->IdMat && $enseignementItem->IdNiv == $inscriptionItem->IdNiv){
                        $enseignementItem->NbrEtu++;
                    }
                }
                $enseignementItem->update([
                    'NbrEtu' => $enseignementItem->NbrEtu
                ]);
            }

            foreach ($salaires as $salaire) {
                $totalPaiements = 0;

                foreach ($inscriptions as $inscription) {
                    // Recherche du paiement pour cette inscription et ce mois
                    $paiment = Paiment::where('IdEtu', $inscription->IdEtu)
                        ->whereYear('Date_Paiment', date('Y', strtotime($salaire->Date_Salaire)))
                        ->whereMonth('Date_Paiment', date('m', strtotime($salaire->Date_Salaire)))
                        ->first();

                    // Vérification de l'existence du paiement et de son état
                    if ($paiment && $paiment->Etat == 'Payé') {
                        // Calcul du total des paiements pour ce mois
                        $enseignement = Enseignement::where('IdProf', $professeur->id)
                            ->where('IdFil', $inscription->IdFil)
                            ->where('IdMat', $inscription->IdMat)
                            ->where('IdNiv', $inscription->IdNiv)
                            ->first();
                        if($enseignement) $totalPaiements += $enseignement->SalaireParEtu;

                    }
                }

                // Mise à jour du salaire avec le reste à payer et le total des paiements
                $salaire->update([
                    'Reste' => $professeur->SommeApaye,
                    'Montant_actuel' => $totalPaiements,
                ]);
            }


        }
    }




    public function getUsersForSalaire(Request $request)
    {
        // dd($request->input('date'));
        $datePourAfficher = $request->input('date'); // Récupération du mois sélectionné depuis la requête

        // Récupérer tous les étudiants
        // $users = Professeur::whereRaw('DATE_FORMAT(Date_debut, "%Y-%m") <= ?', [$datePourAfficher])->latest()->get();
        // $users = Professeur::latest()->get();
        $users = Professeur::whereRaw('DATE_FORMAT(Date_debut, "%Y-%m") <= ?', [$datePourAfficher])->latest()->get();

        // Initialiser le tableau résultat
        $result = [];

        foreach ($users as $user) {
            // Convertir la date sélectionnée en objet DateTime pour extraire le mois et l'année
            $dateAfficher = new DateTime($datePourAfficher);
            $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
            $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée

            // Vérifier s'il existe un paiement pour cet étudiant et ce mois
            $Salaire = Salaires::where('IdProf', $user->id)
                ->whereRaw('MONTH(Date_Salaire) = ?', [$mois])
                ->whereRaw('YEAR(Date_Salaire) = ?', [$annee])
                ->first();


            if (!$Salaire) {
                // Si aucun paiement trouvé, ajouter les détails par défaut
                $result[] = [
                    'id' => $user->id,
                    'Nom' => $user->Nom,
                    'Prenom' => $user->Prenom,
                    'SommeApaye' => $user->SommeApaye,
                    'Etat' => 'Non payé',
                    'Montant' => '0',
                    'Montant_actuel' => '0',
                    'Reste' => $user->SommeApaye,
                    'DatePaiment' => '',
                ];
            } else {
                if(($Salaire->Etat == 'Non payé') && ($Salaire->Reste < $Salaire->Montant_actuel))
                {
                    $Salaire->update([
                                'Reste' => $Salaire->Montant_actuel,
                            ]);
                }

                // Si un paiement est trouvé, ajouter les détails du paiement
                $result[] = [
                    'id' => $user->id,
                    'Nom' => $user->Nom,
                    'Prenom' => $user->Prenom,
                    'SommeApaye' => $user->SommeApaye,
                    'Etat' => $Salaire->Etat,
                    'Montant' => $Salaire->Montant,
                    'Montant_actuel' => $Salaire->Montant_actuel !== null ? $Salaire->Montant_actuel : 0,
                    'Reste' => $Salaire->Reste,
                    'DatePaiment' => $Salaire->Date_Salaire,
                ];
            }
        }

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function storeMatiere(){
        request()->validate([
            'Libelle' => 'required',

        ]);

        return Matiere::create([
            'Libelle' => request('Libelle'),

        ]);

    }



    public function storeNiveau(){
        request()->validate([
            'Nom' => 'required',

        ]);

        return Niveau::create([
            'Nom' => request('Nom'),

        ]);

    }



    public function storeFiliere(){
        request()->validate([
            'Intitule' => 'required',
            'niv' => 'required',

        ]);

         // Récupération de l'ID du niveau (IdNiv) depuis la table Niveau
    $niveau = Niveau::where('Nom', request('niv'))->first();
    $idNiv = $niveau ? $niveau->id : null; // ID du niveau ou null si non trouvé


        return Filiere::create([
            'Intitule' => request('Intitule'),
            'IdNiv' => $idNiv,

        ]);

    }







    public function store()
    {

        $NbrMat = 0;



        request()->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'niv' => 'required',
            'fil' => 'required',
            'matieres' => 'required|array', // Assurez-vous que 'matieres' est un tableau
            'professeurs' => 'required|array',
            'Date_debut' => 'required', // Assurez-vous que 'matieres' est un tableau

        ]);

        // Récupération de l'ID du niveau (IdNiv) depuis la table Niveau
        $niveau = Niveau::where('Nom', request('niv'))->first();
        $idNiv = $niveau ? $niveau->id : null; // ID du niveau ou null si non trouvé

        // Récupération de l'ID de la filière (IdFil) depuis la table Filiere
        $filiere = Filiere::where('Intitule', request('fil'))->first();
        $idFil = $filiere ? $filiere->id : null; // ID de la filière ou null si non trouvée

        $dateDebut = request('Date_debut') . '-01'; // Ajoutez le jour 01 pour former une date complète au format YYYY-MM-DD
        // dd($dateDebut);


        // Création de l'étudiant avec les IDs récupérés
        $etudiant = Etudiant::create([
            'Nom' => request('nom'),
            'Prenom' => request('prenom'),
            'Tele' => request('tele'),
            'Adresse' => request('adresse'),
            'IdNiv' => $idNiv,
            'IdFil' => $idFil,
            'Date_debut' => $dateDebut,
        ]);

        // dd($etudiant);

         // Enregistrement des matières sélectionnées pour cet étudiant
         $matieres = request('matieres');
         $professeurs = request('professeurs');
        //  dd(count($professeurs));
         foreach ($matieres as $index => $matiere) {
             $matiere = Matiere::where('Libelle', $matiere)->first();
             $idMat = $matiere ? $matiere->id : null;
             $NbrMat = $NbrMat +1;

             if($index<count($professeurs)){
                $professeur=Professeur::where('Nom',$professeurs[$index])->first();




                Inscription::create([
                    'IdEtu' => $etudiant->id,
                    'IdMat' => $idMat,
                    'IdProf' => $professeur->id,
                    'IdNiv'=> $idNiv,
                    'IdFil' => $idFil,
                ]);

                // else
                // {
                //     $existInsc->update([
                //         'IdEtu' => $etudiant->id,
                //         'IdMat' => $idMat,
                //         'IdProf' => $professeur->id,
                //         'IdNiv'=> $idNiv,
                //         'IdFil' => $idFil,
                //     ]);
                // }




                $nbrEtus = $this->NbrEtuParProf($professeur->id,$idNiv,$idFil,$idMat);


                $val_salaire = Enseignement::where('IdProf',$professeur->id)->get();
                $totalSalire=0;
                foreach($val_salaire as $val){
                    $totalSalire=$totalSalire+$val->Somme;

                }
                $professeur->update([
                    'SommeApaye' =>  $totalSalire,
                ]);

                //debut de modification de reste dans la table salaire




                $datePourAfficher = $dateDebut; // Récupération du mois sélectionné depuis la requête
                $dateAfficher = new DateTime($datePourAfficher);
                $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
                $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée

                $inscriptions = Inscription::where('IdEtu',$etudiant->id)->get();
                // dd($inscriptions);
                foreach($inscriptions as $insc){
                    $enseignement = Enseignement::where('IdProf',$insc->IdProf)->where('IdFil',$insc->IdFil)
                    ->where('IdMat',$insc->IdNiv)->where('IdNiv',$insc->IdNiv)->first();
                    // dd($insc->IdProf);

                    $Salaire = Salaires::where('IdProf',$insc->IdProf)
                    ->whereRaw('MONTH(Date_Salaire) = ?', [$mois])
                    ->whereRaw('YEAR(Date_Salaire) = ?', [$annee])
                    ->first();

                    $profForSomme = Professeur::find($insc->IdProf);
                    // $Salaire->Montant_actuel = $Salaire->Montant_actuel + $enseignement->SalaireParEtu;
                    // $Salaire->save();
            if($Salaire){
                    $Salaire->update([
                        'Reste' => $profForSomme->SommeApaye - $Salaire->Montant,
                    ]);
                }



                }





                //fin de modification de reste dans la table salaire

                // dd($val_salaire);

                // dd($nbrEtus);
             }
             else{
             Inscription::create([
                 'IdEtu' => $etudiant->id,
                 'IdMat' => $idMat,
                 'IdNiv'=> $idNiv,
                 'IdFil' => $idFil,
             ]);
            }
         }

         $valeur_paiment = Valeurs_Paiments::where('IdNiv', $idNiv)->where('NbrMat', $NbrMat)->first();

         $etudiant->update([
            'SommeApaye' => $valeur_paiment->Valeur,
        ]);





        // return $etudiant;
        return [
            'Nom' => request('nom'),
            'Prenom' => request('prenom'),
            'Tele' => request('tele'),
            'Adresse' => request('adresse'),
            'IdNiv' => $idNiv,
            'IdFil' => $idFil,
            'Date_debut' => request('Date_debut'),
        ];
    }


    public function NbrEtuParProf($IdProf,$idNiv,$idFil,$idMat){
        $insc = Inscription::where('IdProf',$IdProf)->where('IdNiv',$idNiv)->where('IdFil',$idFil)->where('IdMat',$idMat)->get();
        // dd($idNiv);
        $enseignement = Enseignement::where('IdProf',$IdProf)->where('IdNiv',$idNiv)->where('IdFil',$idFil)->where('IdMat',$idMat)->first();
        // dd($enseignement);

if($enseignement){
        $enseignement->update([
            'NbrEtu' => count($insc),

        ]);
    }

       return $enseignement;
    }

    public function deleteInsc($IdProf,$idNiv,$idFil,$idMat,$IdEtu){
        $insc = Inscription::where('IdProf',$IdProf)->where('IdEtu',$IdEtu)->where('IdNiv',$idNiv)->where('IdFil',$idFil)->where('IdMat',$idMat)->first();


if($insc){
    $deleted=$insc->delete();
    }

       return $deleted;
    }

    public function DecNbrEtuParProf($IdProf,$idNiv,$idFil,$idMat){
        $insc = Inscription::where('IdProf',$IdProf)->where('IdNiv',$idNiv)->where('IdFil',$idFil)->where('IdMat',$idMat)->get();
        // dd(count($insc));
        $enseignement = Enseignement::where('IdProf',$IdProf)->where('IdNiv',$idNiv)->where('IdFil',$idFil)->where('IdMat',$idMat)->first();
if($enseignement){
        $enseignement->update([
            'NbrEtu' => count($insc)-1,

        ]);
    }

       return $enseignement;
    }








    public function updateMatiere(Matiere $user)
    {

        request()->validate([
            'Libelle' => 'required',

        ]);
        $user->update([
            'Libelle' => request('Libelle'),

        ]);
        return $user;
    }


    public function updateNiveau(Niveau $user)
    {

        request()->validate([
            'Nom' => 'required',

        ]);
        $user->update([
            'Nom' => request('Nom'),

        ]);
        return $user;
    }



    public function updateFiliere(Filiere $user)
    {

        request()->validate([
            'Intitule' => 'required',
            'niv' => 'required',


        ]);

         // Récupération de l'ID du niveau (IdNiv) depuis la table Niveau
         $niveau = Niveau::where('Nom', request('niv'))->first();
         $idNiv = $niveau ? $niveau->id : null;


        $user->update([
            'Intitule' => request('Intitule'),
            'IdNiv' => $idNiv,

        ]);
        return $user;
    }





    public function update(Etudiant $user)
    {
        // Validation des données
        request()->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'niv' => 'required',
            'fil' => 'required',
            'matieres' => 'required|array',
            'professeurs' => 'required|array',
            'Date_debut' => 'required',

        ]);


        // Récupération de l'ID du niveau (IdNiv) depuis la table Niveau
        $niveau = Niveau::where('Nom', request('niv'))->first();
        $idNiv = $niveau ? $niveau->id : null;

        // Récupération de l'ID de la filière (IdFil) depuis la table Filiere
        $filiere = Filiere::where('Intitule', request('fil'))->first();
        $idFil = $filiere ? $filiere->id : null;

        $matiere = Matiere::where('Libelle', request('matieres'))->first();
        $idMat = $matiere ? $matiere->id : null;
        // dd($idMat);

        // Récupération de l'étudiant
        $user = Etudiant::findOrFail($user->id);
        // dd($user);


        // Stocker les matières actuelles de l'étudiant
        $currentMatieres = $user->matieres()->pluck('Libelle')->toArray();
        // $currentProfesseurs = $user->professeurs()->pluck('Nom')->toArray();

        $dateDebut = request('Date_debut') . '-01'; // Ajoutez le jour 01 pour former une date complète au format YYYY-MM-DD

        $inscription = Inscription::where('IdEtu', $user->id)->first();
        // dd($inscription);


        $NbrMat=0;


        $inscDeleted= $this->deleteInsc($inscription->IdProf,$inscription->IdNiv,$inscription->IdFil,$inscription->IdMat,$user->id);

        if($inscDeleted){
        $nbrEtusDec = $this->NbrEtuParProf($inscription->IdProf,$inscription->IdNiv,$inscription->IdFil,$inscription->IdMat);
        }


        $matieres = request('matieres');
        $professeurs = request('professeurs');
       //  dd(count($professeurs));
        foreach ($matieres as $index => $matiere) {
            $matiere = Matiere::where('Libelle', $matiere)->first();
            $idMat = $matiere ? $matiere->id : null;
            $NbrMat = $NbrMat +1;

            if($index<count($professeurs)){
               $professeur=Professeur::where('Nom',$professeurs[$index])->first();




               Inscription::create([
                   'IdEtu' => $user->id,
                   'IdMat' => $idMat,
                   'IdProf' => $professeur->id,
                   'IdNiv'=> $idNiv,
                   'IdFil' => $idFil,
               ]);

                $nbrEtusIncFirst = $this->NbrEtuParProf($professeur->id,$idNiv,$idFil,$idMat);





            }
            else{
            Inscription::create([
                'IdEtu' => $user->id,
                'IdMat' => $idMat,
                'IdNiv'=> $idNiv,
                'IdFil' => $idFil,
            ]);
           }
           $valeur_paiment = Valeurs_Paiments::where('IdNiv', $idNiv)->where('NbrMat', $NbrMat)->first();
           $paiment = Paiment::where('IdEtu',$user->id)->get();
        //    dd($paiment);
           foreach($paiment as $p){

            // dd($user->SommeApaye);
            $p->update([
                'SommeApaye' => $user->SommeApaye,
            ]);
            // dd($p->SommeApaye);
           }



           $user->update([
           'SommeApaye' => $valeur_paiment->Valeur,
       ]);


        }





        // Vérifier si les matières ont été modifiées
        if (array_diff($currentMatieres, request('matieres')) || array_diff(request('matieres'), $currentMatieres)) {
            // Si les matières ont été modifiées, procéder à la mise à jour

            // Enregistrement des anciennes matières dans l'historique
            $oldMatiereString = implode(',', $currentMatieres);
            Historique_Inscriptions::create([
                'IdEtu' => $user->id,
                'ListeMateres' => $oldMatiereString,
            ]);



            // Mise à jour des informations de l'étudiant
            $user->update([
                'Nom' => request('nom'),
                'Prenom' => request('prenom'),
                'Tele' => request('tele'),
                'Adresse' => request('adresse'),
                'IdNiv' => $idNiv,
                'IdFil' => $idFil,
                'Date_debut' => $dateDebut,
            ]);

            // Récupération des identifiants des matières à partir de leurs libellés
            $matiereNames = request('matieres');
            $matiereIds = Matiere::whereIn('Libelle', $matiereNames)->pluck('id')->toArray();

            $professeurNames = request('professeurs');
            $professeurIds = Professeur::whereIn('Nom', $professeurNames)->pluck('id')->toArray();

            // Mise à jour des matières sélectionnées pour cet étudiant
            $user->matieres()->sync($matiereIds);
            $user->professeurs()->sync($professeurIds);
            // $this->AllNbrEtuParProf();

            $nbrEtusInc = $this->NbrEtuParProf($inscription->IdProf,$idNiv,$idFil,$idMat);



        $professeur = Professeur::where('id',$inscription->IdProf)->first();



        $val_salaire = Enseignement::where('IdProf',$inscription->IdProf)->get();
                $totalSalire=0;
                foreach($val_salaire as $val){
                    $totalSalire=$totalSalire+$val->Somme;

                }
                $professeur->update([
                    'SommeApaye' =>  $totalSalire,
                ]);



        } else {
            // Si les matières n'ont pas été modifiées, procéder à la mise à jour des autres informations seulement


            // $nbrEtusDec = $this->DecNbrEtuParProf($inscription->IdProf,$inscription->IdNiv,$inscription->IdFil,$inscription->IdMat);


            // Mise à jour des autres informations de l'étudiant
            $user->update([
                'Nom' => request('nom'),
                'Prenom' => request('prenom'),
                'Tele' => request('tele'),
                'Adresse' => request('adresse'),
                'IdNiv' => $idNiv,
                'IdFil' => $idFil,
                'Date_debut' => $dateDebut,
            ]);



            $professeurNames = request('professeurs');
            $professeurIds = Professeur::whereIn('Nom', $professeurNames)->pluck('id')->toArray();
            $user->professeurs()->sync($professeurIds);

            $nbrEtusInc = $this->NbrEtuParProf($inscription->IdProf,$idNiv,$idFil,$idMat);
            // dd($nbrEtusInc->NbrEtu);



        $professeur = Professeur::where('id',$inscription->IdProf)->first();



        $val_salaire = Enseignement::where('IdProf',$inscription->IdProf)->get();
                $totalSalire=0;
                foreach($val_salaire as $val){
                    $totalSalire=$totalSalire+$val->Somme;

                }
                $professeur->update([
                    'SommeApaye' =>  $totalSalire,
                ]);


            // $this->AllNbrEtuParProf();
        }

        return $user;
    }


    public function marquerPresence(Request $request)
    {

       // Récupérez toutes les données envoyées via la requête
       $data = $request->all();
    //    dd($data);

       $filiere_infos = explode("\n", $data['data']["Filiere"]);

       $niv = $filiere_infos[0];
       $fili = $filiere_infos[1];
       $mat = $filiere_infos[2];

       $filiere = Filiere::where('Intitule', $fili)->first();

        $niveau = Niveau::where('Nom', $niv)->first();
        $matiere = Matiere::where('Libelle', $mat)->first();

       $IdProf = $data['ProfId'];

       $date = $data['selectedMonth'];
    //    dd($data['data']["id"]);


       $absence=Absence::where('IdEtu', $data['data']["id"])
                    ->where('IdMat',$matiere->id)
                    ->where('IdFil',$filiere->id)
                    ->where('IdProf',$IdProf)
                    ->where('IdNiv',$niveau->id)
                    ->where('Date_absence',$date)
                    ->first();
                    // dd($absence);
        if($absence){
            $absence->update([
                'Status' => 'Présent',
            ]);
        }
        else
        {
       $absence=Absence::create([
        'IdEtu' =>  $data['data']["id"],
        'IdMat' => $matiere->id,
        'IdFil' => $filiere->id,
        'IdProf' => $IdProf,
        'IdNiv' => $niveau->id,
        'Status' => 'Présent',
        'Date_absence' => $date,

    ]);
     }




    }


    public function marquerAbsence(Request $request)
    {

       // Récupérez toutes les données envoyées via la requête
       $data = $request->all();
    //    dd($data);

       $filiere_infos = explode("\n", $data['data']["Filiere"]);

       $niv = $filiere_infos[0];
       $fili = $filiere_infos[1];
       $mat = $filiere_infos[2];

       $filiere = Filiere::where('Intitule', $fili)->first();

        $niveau = Niveau::where('Nom', $niv)->first();
        $matiere = Matiere::where('Libelle', $mat)->first();

       $IdProf = $data['ProfId'];

       $date = $data['selectedMonth'];
    //    dd($data['data']["id"]);


       $absence=Absence::where('IdEtu', $data['data']["id"])
                    ->where('IdMat',$matiere->id)
                    ->where('IdFil',$filiere->id)
                    ->where('IdProf',$IdProf)
                    ->where('IdNiv',$niveau->id)
                    ->where('Date_absence',$date)
                    ->first();
                    // dd($absence);
        if($absence){
            $absence->update([
                'Status' => 'Absent',
            ]);
        }
        else
        {
       $absence=Absence::create([
        'IdEtu' =>  $data['data']["id"],
        'IdMat' => $matiere->id,
        'IdFil' => $filiere->id,
        'IdProf' => $IdProf,
        'IdNiv' => $niveau->id,
        'Status' => 'Absent',
        'Date_absence' => $date,

    ]);
     }




    }


    public function effectuerPaiement(Etudiant $user)
    {

        request()->validate([
            'SommeApaye' => 'required',
            'Montant' => 'required',
            'DatePaiment' => 'required',
        ]);



        // Mise à jour des informations de l'étudiant
        $user->update([
            'SommeApaye' => request('SommeApaye'),
        ]);

        $etat = 'Non payé';

        if (request('Montant') == 0) {
            $etat = 'Non payé';
        } elseif ($user->SommeApaye == request('Montant')) {
            $etat = 'Payé';
        } elseif ($user->SommeApaye > request('Montant')) {
            $etat = 'Semi payé';
        } else {
            $etat = 'Payé et plus';
        }

        //calcul de salaire actuelle pour prof  -----------------------
if($etat == 'Payé'){

    $datePourAfficher = request('DatePaiment'); // Récupération du mois sélectionné depuis la requête
    $dateAfficher = new DateTime($datePourAfficher);
    $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
    $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée

    $inscriptions = Inscription::where('IdEtu',$user->id)->get();
    // dd($inscriptions);
    foreach($inscriptions as $insc){
        $enseignement = Enseignement::where('IdProf',$insc->IdProf)->where('IdFil',$insc->IdFil)
        ->where('IdMat',$insc->IdMat)->where('IdNiv',$insc->IdNiv)->first();
        // dd($enseignement);

        $Salaire = Salaires::where('IdProf',$insc->IdProf)
        ->whereRaw('MONTH(Date_Salaire) = ?', [$mois])
        ->whereRaw('YEAR(Date_Salaire) = ?', [$annee])
        ->first();

        $profForSomme = Professeur::find($insc->IdProf);
        // $Salaire->Montant_actuel = $Salaire->Montant_actuel + $enseignement->SalaireParEtu;
        // $Salaire->save();
if(($Salaire) && ($Salaire->Montant_actuel<$profForSomme->SommeApaye)){
        $Salaire->update([
            'Montant_actuel' => $Salaire->Montant_actuel + $enseignement->SalaireParEtu,
            'Reste' => $profForSomme->SommeApaye - $Salaire->Montant,
        ]);
    }
    else{
        // debut de creation de salaire vide
        $etat2 = 'Non payé';



        $reste2 =$profForSomme ? $profForSomme->SommeApaye : 0;




        $montant_actuel = $enseignement ? $enseignement->SalaireParEtu : 0;

        Salaires::create([
            'Montant' => 0,
            'Montant_actuel' => $montant_actuel,
            'Reste' => $reste2,
            'Etat' => $etat2,
            'Date_Salaire' => request('DatePaiment'),
            'IdProf' => $profForSomme->id,
        ]);


        //fin de création de salaire vide
    }


    }

}


        //fin de calcule de salaire actuell pour prof ------------------------

        $reste = $user->SommeApaye - request('Montant');

        $datePourAfficher = request('DatePaiment'); // Récupération du mois sélectionné depuis la requête
        $dateAfficher = new DateTime($datePourAfficher);
        $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
        $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée

        $niveau = Niveau::where('id',$user->IdNiv)->first();

        $matieres = $user->matieres()->pluck('Libelle')->toArray();






        $paiment = Paiment::where('IdEtu', $user->id)
        ->whereRaw('MONTH(Date_Paiment) = ?', [$mois])
        ->whereRaw('YEAR(Date_Paiment) = ?', [$annee])
        ->first();

        if (!$paiment) {
            // Création d'un paiement pour cet étudiant
            $paiment=Paiment::create([
                'Montant' => request('Montant'),
                'Reste' => $reste,
                'Etat' => $etat,
                'Date_Paiment' => request('DatePaiment'),
                'IdEtu' => $user->id,
            ]);
        } else {
            // Mise à jour du paiement existant pour cet étudiant
            $paiment->update([
                'Montant' => request('Montant'),
                'Reste' => $reste,
                'Etat' => $etat,
                'Date_Paiment' => request('DatePaiment'),
            ]);
        }
        $result = [
            'Nom' => $user->Nom,
            'Prenom' => $user->Prenom,
            'SommeApaye' => $user->SommeApaye,
            'Etat' => $paiment->Etat,
            'Montant' => $paiment->Montant,
            'Reste' => $paiment->Reste,
            'DatePaiment' => $paiment->Date_Paiment,
            'Niveau' => $niveau->Nom,
            'Matieres' => $matieres,
        ];

        // Après avoir effectué le paiement, récupérez les informations mises à jour et renvoyez-les
        return $result;
    }



    public function effectuerSalaire(Professeur $user)
    {
        request()->validate([
            'SommeApaye' => 'required',
            'Montant' => 'required',
            'DatePaiment' => 'required',
        ]);

        // Mise à jour des informations de l'étudiant
        $user->update([
            'SommeApaye' => request('SommeApaye'),
        ]);

        // dd($user);

        $etat = 'Non payé';

        if (request('Montant') == 0) {
            $etat = 'Non payé';
        } elseif ($user->SommeApaye == request('Montant')) {
            $etat = 'Payé';
        } elseif ($user->SommeApaye > request('Montant')) {
            $etat = 'Semi payé';
        } else {
            $etat = 'Payé et plus';
        }

        $reste = $user->SommeApaye - request('Montant');

        // dd(request('DatePaiment'));

        $datePourAfficher = request('DatePaiment'); // Récupération du mois sélectionné depuis la requête
        $dateAfficher = new DateTime($datePourAfficher);
        $mois = $dateAfficher->format('m'); // Mois de la date sélectionnée
        $annee = $dateAfficher->format('Y'); // Année de la date sélectionnée


        $salaire = Salaires::where('IdProf', $user->id)
        ->whereRaw('MONTH(Date_Salaire) = ?', [$mois])
        ->whereRaw('YEAR(Date_Salaire) = ?', [$annee])
        ->first();



        // dd($salaire);

        if (!$salaire) {
            // Création d'un paiement pour cet étudiant
            Salaires::create([
                'Montant' => request('Montant'),
                'Reste' => $reste,
                'Etat' => $etat,
                'Date_Salaire' => request('DatePaiment'),
                'IdProf' => $user->id,
            ]);
        }
        else {
            // Mise à jour du paiement existant pour cet étudiant
            $salaire->update([
                'Montant' => request('Montant'),
                'Reste' => $reste,
                'Etat' => $etat,
                'Date_Salaire' => request('DatePaiment'),
            ]);
        }

        // Après avoir effectué le paiement, récupérez les informations mises à jour et renvoyez-les
        return $this->getUsersForSalaire(request());
    }




    public function getNiveaux(){
        $niveaux = Niveau::pluck('Nom', 'id'); // Récupérer les données de la colonne 'Nom' avec l'ID


    return $niveaux;


    }

    public function getNbrMat() {
        $nbrMat = Matiere::count(); // Compter le nombre total de lignes dans la table Matieres

        return $nbrMat;
    }

    public function getValeurPaiments() {
        $valeursPaiements = Valeurs_Paiments::all();

        // Structurez les données sous forme de tableau associatif correspondant à votre utilisation dans Vue.js
        $data = [];

        foreach ($valeursPaiements as $vp) {
            $data[$vp->IdNiv][$vp->NbrMat] = $vp->Valeur;
        }

        return $data;
    }

    public function getValeurSalaires() {
        $valeursPaiements = Valeurs_Salaires::all();

        // Structurez les données sous forme de tableau associatif correspondant à votre utilisation dans Vue.js
        $data = [];

        foreach ($valeursPaiements as $vp) {
            $data[$vp->IdNiv][$vp->IdMat] = $vp->Valeur;
        }

        return $data;
    }



    public function updateValPaiment(Request $request)
    {
        try {
            // Récupérez toutes les données envoyées via la requête
            $data = $request->all();
            // dd($data);

            // Récupérez NivToEdit
            $nivToEdit = $data['NivToEdit'];
            $niveau = Niveau::where('Nom', $nivToEdit)->first();

            $nbr=0;


            // Parcourez les données et mettez à jour chaque valeur de paiement
            foreach ($data['formValues'] as $val) {
                $nbr=$nbr+1;
                $valeur_paiement = $val['Valeur_paiment']; // Récupérez la valeur de paiement mise à jour

                $valeurs_paiments = Valeurs_Paiments::where('IdNiv', $niveau->id)->where('NbrMat', $nbr)->first();

                if ($valeurs_paiments) {

                $valeurs_paiments->update(['Valeur' => $valeur_paiement]);
                } else{
                    dd($nbr);
                }
            }

            // Réponse de succès
            return response()->json(['message' => 'Mise à jour réussie'], 200);
        } catch (\Exception $e) {
            // En cas d'erreur, retournez un message d'erreur
            return response()->json(['message' => 'Erreur lors de la mise à jour', 'error' => $e->getMessage()], 500);
        }
    }



    public function updateValSalaire(Request $request)
    {
        try {
            // Récupérez toutes les données envoyées via la requête
            $data = $request->all();
            // dd($data);

            // Récupérez NivToEdit
            $nivToEdit = $data['NivToEdit'];
            $niveau = Niveau::where('Nom', $nivToEdit)->first();

            $nbr=0;


            // Parcourez les données et mettez à jour chaque valeur de paiement
            foreach ($data['formValues'] as $val) {
                $nbr=$nbr+1;

                $valeur_paiement = $val['Valeur_paiment']; // Récupérez la valeur de paiement mise à jour

                $valeurs_paiments = Valeurs_Salaires::where('IdNiv', $niveau->id)->where('IdMat', $nbr)->first();

                if ($valeurs_paiments) {

                $valeurs_paiments->update(['Valeur' => $valeur_paiement]);
                }
               else{
                $valeurs_paiments = Valeurs_Salaires::where('IdNiv', $niveau->id)->where('IdMat', 6)->first();
                $valeurs_paiments->update(['Valeur' => $valeur_paiement]);
               }
            }

            // Réponse de succès
            return response()->json(['message' => 'Mise à jour réussie'], 200);
        } catch (\Exception $e) {
            // En cas d'erreur, retournez un message d'erreur
            return response()->json(['message' => 'Erreur lors de la mise à jour', 'error' => $e->getMessage()], 500);
        }
    }





    public function getFilieresList($Niveaux){
        // Récupérer tous les niveaux ayant le nom donné
        $niveauxArray = explode(',', $Niveaux);





        $filieres = []; // Initialiser un tableau vide pour stocker les filières

        // Parcourir tous les niveaux correspondants
        foreach ($niveauxArray as $niveau) {
            // Récupérer les filières correspondant à chaque ID de niveau

            $niv = Niveau::where('Nom', $niveau)->first();


            $filiere = Filiere::where('IdNiv', $niv->id)->pluck('Intitule', 'id')->toArray();



            // Fusionner les résultats dans le tableau $filieres
            $filieres = array_merge($filieres, $filiere);
        }

        // dd($filieres);



        return $filieres; // Retourner les filières sous forme de tableau
    }







    public function getFilieres($nomNiv){
        // Récupérer tous les niveaux ayant le nom donné
        // dd($nomNiv);
        $niveaux = Niveau::where('Nom', $nomNiv)->get();
        // dd($niveaux);

        $filieres = collect(); // Initialiser une collection vide

        // Parcourir tous les niveaux correspondants
        foreach ($niveaux as $niveau) {
            // Récupérer les filières correspondant à chaque ID de niveau
            $filiere = Filiere::where('IdNiv', $niveau->id)->pluck('Intitule', 'id');

            // Fusionner les résultats dans la collection
            $filieres = $filieres->merge($filiere);
        }

        return $filieres->toArray(); // Retourner les filières sous forme de tableau
    }


    public function getProfesseurPourMatieres($matieres,$niv,$fil){
        $matieresArray = explode(',', $matieres); // Séparer la chaîne en un tableau de matières
        $filiere = Filiere::where('Intitule', $fil)->first();
        $niveau = Niveau::where('Nom', $niv)->first();

        // dd($matieresArray);

        $professeurs = collect(); // Initialiser une collection vide

        foreach($matieresArray as $mat){
            $matiere = Matiere::where('Libelle', $mat)->first();





            // dd($matiere->Libelle);
            if($matiere){
                $enseignements = Enseignement::where('IdMat', $matiere->id)->where('IdNiv',$niveau->id)->where('IdFil',$filiere->id)->get();

                foreach($enseignements as $ens){

                    $prof = Professeur::where('id', $ens->IdProf)->pluck('Nom', 'Prenom');
                    $professeurs = $professeurs->merge($prof);
                }
            }
        }
        // dd($professeurs);

        return $professeurs->toArray(); // Retourner les professeurs sous forme de tableau
    }




    public function getMatieres(){

        $matieres = Matiere::pluck('Libelle', 'id');


        return $matieres;
    }


    public function getMatiere()
    {
        $users = Matiere::latest()->get()->map(function ($user){



            return [
                'id' => $user->id,
                'Libelle' => $user->Libelle,
                'created_at' => $user->created_at ? $user->created_at->format(config('app.date_format')) : null,
            ];
        });

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function getNiveau()
    {
        $users = Niveau::latest()->get()->map(function ($user){



            return [
                'id' => $user->id,
                'Nom' => $user->Nom,
                'created_at' => $user->created_at ? $user->created_at->format(config('app.date_format')) : null,
            ];
        });

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }



    public function getFiliere()
    {
        $users = Filiere::latest()->get()->map(function ($user){

            $niveau = Niveau::where('id',$user->IdNiv)->first();



            return [
                'id' => $user->id,
                'Intitule' => $user->Intitule,
                'IdNiv' => $niveau->Nom,
                'created_at' => $user->created_at ? $user->created_at->format(config('app.date_format')) : null,
            ];
        });

        return response()->json($users, 200, [], JSON_UNESCAPED_UNICODE);
    }










    public function AllNbrEtuParProf()
    {
        $ensg = Enseignement::all();

        foreach ($ensg as $ens) {
            $insc = Inscription::where('IdProf', $ens->IdProf)->get();
            $ens->update([
                'NbrEtu' => count($insc),
            ]);
        }
    }






    public function destory(Etudiant $user)
    {
        $inscription = Inscription::where('IdEtu', $user->id)->first();
        $nbrEtusDec = $this->DecNbrEtuParProf($inscription->IdProf,$inscription->IdNiv,$inscription->IdFil,$inscription->IdMat);

        $professeurs = Professeur::where('id',$inscription->IdProf)->get();
        $user->delete();


    foreach($professeurs as $professeur){
        $val_salaire = Enseignement::where('IdProf',$inscription->IdProf)->get();
                $totalSalire=0;
                foreach($val_salaire as $val){
                    $totalSalire=$totalSalire+$val->Somme;

                }
                $professeur->update([
                    'SommeApaye' =>  $totalSalire,
                ]);

            $salaires = Salaires::where('IdProf', $professeur->id)
            ->where('Etat', '=', 'Non payé')
            ->where(DB::raw('MONTH(Date_Salaire)'), '>=', Carbon::now()->month)
            ->where(DB::raw('YEAR(Date_Salaire)'), '>=', Carbon::now()->year)
            ->get();

            // dd($salaires);
            foreach($salaires as $salaire){

                $salaire->update([
                    'Reste' =>  $totalSalire,
                ]);
            }
        }

        // $this->AllNbrEtuParProf();

        return response()->noContent();
    }

    public function destoryMatiere(Matiere $user)
    {

        $user->delete();


        return response()->noContent();
    }

    public function destoryNiveau(Niveau $user)
    {

        $user->delete();


        return response()->noContent();
    }



    public function destoryFiliere(Filiere $user)
    {

        $user->delete();


        return response()->noContent();
    }




}