<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\Professeur;
use App\Models\Matiere;
use App\Models\Paiement;
use App\Models\Classe;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Récupère les statistiques pour le tableau de bord
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $twoMonthsAgo = $now->copy()->subMonths(2);

        // Récupérer les statistiques selon le rôle de l'utilisateur
        if ($user->hasRole('admin') || $user->hasRole('assistant')) {
            // Statistiques pour les administrateurs et assistants
            $stats = [
                'students' => $this->getStudentsStats($now, $lastMonth, $twoMonthsAgo),
                'teachers' => $this->getTeachersStats($now, $lastMonth, $twoMonthsAgo),
                'courses' => $this->getCoursesStats($now, $lastMonth, $twoMonthsAgo),
                'payments' => $this->getPaymentsStats($now, $lastMonth, $twoMonthsAgo),
            ];
        } elseif ($user->hasRole('professeur')) {
            // Statistiques pour les professeurs
            $professeur = Professeur::where('user_id', $user->id)->firstOrFail();
            $stats = [
                'students' => $this->getTeacherStudentsStats($professeur, $now, $lastMonth, $twoMonthsAgo),
                'courses' => $this->getTeacherCoursesStats($professeur, $now, $lastMonth, $twoMonthsAgo),
                'salary' => $this->getTeacherSalaryStats($professeur, $now, $lastMonth, $twoMonthsAgo),
            ];
        } else {
            // Statistiques pour les étudiants (si nécessaire)
            $etudiant = Etudiant::where('user_id', $user->id)->firstOrFail();
            $stats = [
                'courses' => $this->getStudentCoursesStats($etudiant, $now, $lastMonth, $twoMonthsAgo),
                'attendance' => $this->getStudentAttendanceStats($etudiant, $now, $lastMonth, $twoMonthsAgo),
            ];
        }

        return response()->json($stats);
    }

    /**
     * Récupère les prochains cours
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingClasses(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();
        $endOfWeek = $now->copy()->endOfWeek();

        $query = Classe::with(['matiere', 'professeur.user'])
            ->where('date_debut', '>=', $now)
            ->where('date_debut', '<=', $endOfWeek)
            ->orderBy('date_debut');

        // Filtrer selon le rôle
        if ($user->hasRole('professeur')) {
            $professeur = Professeur::where('user_id', $user->id)->firstOrFail();
            $query->where('professeur_id', $professeur->id);
        } elseif ($user->hasRole('etudiant')) {
            $etudiant = Etudiant::where('user_id', $user->id)->firstOrFail();
            $query->whereHas('etudiants', function($q) use ($etudiant) {
                $q->where('etudiant_id', $etudiant->id);
            });
        }

        $classes = $query->take(10)->get()->map(function($classe) {
            return [
                'id' => $classe->id,
                'matiere' => $classe->matiere->nom,
                'professeur' => $classe->professeur->user->name,
                'date_debut' => $classe->date_debut->format('Y-m-d H:i:s'),
                'date_fin' => $classe->date_fin->format('Y-m-d H:i:s'),
                'salle' => $classe->salle,
            ];
        });

        return response()->json($classes);
    }

    /**
     * Récupère les inscriptions récentes
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentRegistrations(Request $request)
    {
        $user = $request->user();
        
        // Vérifier les permissions
        if (!$user->hasAnyRole(['admin', 'assistant'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $recentRegistrations = Etudiant::with(['user', 'niveau'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($etudiant) {
                return [
                    'id' => $etudiant->id,
                    'name' => $etudiant->user->name,
                    'email' => $etudiant->user->email,
                    'niveau' => $etudiant->niveau->nom,
                    'created_at' => $etudiant->created_at->format('Y-m-d H:i:s'),
                    'gender' => $etudiant->genre,
                ];
            });

        return response()->json($recentRegistrations);
    }

    // Méthodes utilitaires pour les statistiques

    private function getStudentsStats($now, $lastMonth, $twoMonthsAgo)
    {
        $currentCount = Etudiant::count();
        $lastMonthCount = Etudiant::where('created_at', '<=', $lastMonth->endOfMonth())->count();
        $twoMonthsAgoCount = Etudiant::where('created_at', '<=', $twoMonthsAgo->endOfMonth())->count();
        
        $change = $lastMonthCount > 0 
            ? round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1)
            : 100;
            
        $previousChange = $twoMonthsAgoCount > 0
            ? round((($lastMonthCount - $twoMonthsAgoCount) / $twoMonthsAgoCount) * 100, 1)
            : ($lastMonthCount > 0 ? 100 : 0);

        return [
            'value' => $currentCount,
            'change' => $change >= 0 ? "+{$change}%" : "{$change}%",
            'change_type' => $change > $previousChange ? 'increase' : ($change < $previousChange ? 'decrease' : 'neutral')
        ];
    }

    private function getTeachersStats($now, $lastMonth, $twoMonthsAgo)
    {
        $currentCount = Professeur::count();
        $lastMonthCount = Professeur::where('created_at', '<=', $lastMonth->endOfMonth())->count();
        
        $change = $lastMonthCount > 0 
            ? round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1)
            : ($currentCount > 0 ? 100 : 0);

        return [
            'value' => $currentCount,
            'change' => $change >= 0 ? "+{$change}%" : "{$change}%",
            'change_type' => $change > 0 ? 'increase' : ($change < 0 ? 'decrease' : 'neutral')
        ];
    }

    private function getCoursesStats($now, $lastMonth, $twoMonthsAgo)
    {
        $currentCount = Matiere::count();
        $lastMonthCount = Matiere::where('created_at', '<=', $lastMonth->endOfMonth())->count();
        
        $change = $lastMonthCount > 0 
            ? round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1)
            : ($currentCount > 0 ? 100 : 0);

        return [
            'value' => $currentCount,
            'change' => $change >= 0 ? "+{$change}%" : "{$change}%",
            'change_type' => $change > 0 ? 'increase' : ($change < 0 ? 'decrease' : 'neutral')
        ];
    }

    private function getPaymentsStats($now, $lastMonth, $twoMonthsAgo)
    {
        $currentMonthTotal = Paiement::whereYear('date_paiement', $now->year)
            ->whereMonth('date_paiement', $now->month)
            ->sum('montant');
            
        $lastMonthTotal = Paiement::whereYear('date_paiement', $lastMonth->year)
            ->whereMonth('date_paiement', $lastMonth->month)
            ->sum('montant');
            
        $twoMonthsAgoTotal = Paiement::whereYear('date_paiement', $twoMonthsAgo->year)
            ->whereMonth('date_paiement', $twoMonthsAgo->month)
            ->sum('montant');
            
        $change = $lastMonthTotal > 0 
            ? round((($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1)
            : ($currentMonthTotal > 0 ? 100 : 0);
            
        $previousChange = $twoMonthsAgoTotal > 0
            ? round((($lastMonthTotal - $twoMonthsAgoTotal) / $twoMonthsAgoTotal) * 100, 1)
            : ($lastMonthTotal > 0 ? 100 : 0);

        return [
            'value' => $currentMonthTotal,
            'change' => $change >= 0 ? "+{$change}%" : "{$change}%",
            'change_type' => $change > $previousChange ? 'increase' : ($change < $previousChange ? 'decrease' : 'neutral')
        ];
    }

    private function getTeacherStudentsStats($professeur, $now, $lastMonth, $twoMonthsAgo)
    {
        $currentCount = $professeur->etudiants()->count();
        
        // Logique pour calculer l'évolution
        $lastMonthCount = $professeur->etudiants()
            ->where('etudiant_professeur.created_at', '<=', $lastMonth->endOfMonth())
            ->count();
            
        $change = $lastMonthCount > 0 
            ? round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1)
            : ($currentCount > 0 ? 100 : 0);

        return [
            'value' => $currentCount,
            'change' => $change >= 0 ? "+{$change}%" : "{$change}%",
            'change_type' => $change > 0 ? 'increase' : ($change < 0 ? 'decrease' : 'neutral')
        ];
    }

    private function getTeacherCoursesStats($professeur, $now, $lastMonth, $twoMonthsAgo)
    {
        $currentCount = $professeur->matieres()->count();
        
        // Logique pour calculer l'évolution
        $lastMonthCount = $professeur->matieres()
            ->where('created_at', '<=', $lastMonth->endOfMonth())
            ->count();
            
        $change = $lastMonthCount > 0 
            ? round((($currentCount - $lastMonthCount) / $lastMonthCount) * 100, 1)
            : ($currentCount > 0 ? 100 : 0);

        return [
            'value' => $currentCount,
            'change' => $change >= 0 ? "+{$change}%" : "{$change}%",
            'change_type' => $change > 0 ? 'increase' : ($change < 0 ? 'decrease' : 'neutral')
        ];
    }

    private function getTeacherSalaryStats($professeur, $now, $lastMonth, $twoMonthsAgo)
    {
        // Implémenter la logique pour récupérer les statistiques de salaire
        // Ceci est un exemple simplifié
        return [
            'value' => 0,
            'change' => '0%',
            'change_type' => 'neutral'
        ];
    }

    private function getStudentCoursesStats($etudiant, $now, $lastMonth, $twoMonthsAgo)
    {
        // Implémenter la logique pour les statistiques des cours de l'étudiant
        return [
            'value' => 0,
            'change' => '0%',
            'change_type' => 'neutral'
        ];
    }

    private function getStudentAttendanceStats($etudiant, $now, $lastMonth, $twoMonthsAgo)
    {
        // Implémenter la logique pour les statistiques de présence de l'étudiant
        return [
            'value' => '100%',
            'change' => '0%',
            'change_type' => 'neutral'
        ];
    }
}
