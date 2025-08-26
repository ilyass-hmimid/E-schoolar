<?php

namespace App\Services;

use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Traits\OptimizedQueries;

class PresenceService
{
    use OptimizedQueries;

    /**
     * Durée de vie du cache par défaut (en minutes)
     */
    protected $cacheTtl = 60;

    /**
     * Récupère les présences avec pagination et filtres
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedPresences($filters = [])
    {
        $cacheKey = 'presences_' . md5(serialize($filters));
        
        return $this->cached($cacheKey, function () use ($filters) {
            $query = Presence::query()
                ->with(['etudiant', 'matiere', 'classe', 'professeur'])
                ->latest('date_seance');

            // Appliquer les filtres
            $this->applyFilters($query, $filters);

            return $query->paginate(config('app.paginate', 15));
        }, $this->cacheTtl, ['presences']);
    }

    /**
     * Applique les filtres à la requête
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyFilters($query, $filters)
    {
        if (!empty($filters['etudiant_id'])) {
            $query->where('etudiant_id', $filters['etudiant_id']);
        }
        
        if (!empty($filters['matiere_id'])) {
            $query->where('matiere_id', $filters['matiere_id']);
        }
        
        if (!empty($filters['classe_id'])) {
            $query->where('classe_id', $filters['classe_id']);
        }
        
        if (!empty($filters['professeur_id'])) {
            $query->where('professeur_id', $filters['professeur_id']);
        }
        
        if (!empty($filters['date_debut'])) {
            $query->whereDate('date_seance', '>=', $filters['date_debut']);
        }
        
        if (!empty($filters['date_fin'])) {
            $query->whereDate('date_seance', '<=', $filters['date_fin']);
        }
        
        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }
    }

    /**
     * Récupère les statistiques des présences
     *
     * @param array $params
     * @return array
     */
    public function getStatistics($params = [])
    {
        return $this->getCachedStatistics($params, function () use ($params) {
            $query = Presence::query();
            
            // Appliquer les filtres
            $this->applyFilters($query, $params);
            
            // Statistiques globales
            $stats = [
                'total' => (clone $query)->count(),
                'presents' => (clone $query)->where('statut', 'present')->count(),
                'absents' => (clone $query)->where('statut', 'absent')->count(),
                'retards' => (clone $query)->where('statut', 'retard')->count(),
                'justifies' => (clone $query)->where('statut', 'justifie')->count(),
            ];
            
            // Calculer les pourcentages
            $stats['taux_presence'] = $stats['total'] > 0 
                ? round(($stats['presents'] / $stats['total']) * 100, 2) 
                : 0;
                
            // Statistiques par matière
            $stats['par_matiere'] = $this->getStatsParMatiere($params);
            
            // Statistiques par classe
            $stats['par_classe'] = $this->getStatsParClasse($params);
            
            // Statistiques temporelles
            $stats['temporelles'] = $this->getStatsTemporelles($params);
            
            return $stats;
        }, $this->cacheTtl);
    }
    
    /**
     * Récupère les statistiques par matière
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getStatsParMatiere($params)
    {
        return DB::table('presences')
            ->join('matieres', 'presences.matiere_id', '=', 'matieres.id')
            ->select(
                'matieres.id',
                'matieres.nom',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN statut = "present" THEN 1 ELSE 0 END) as presents'),
                DB::raw('SUM(CASE WHEN statut = "absent" THEN 1 ELSE 0 END) as absents'),
                DB::raw('SUM(CASE WHEN statut = "retard" THEN 1 ELSE 0 END) as retards'),
                DB::raw('SUM(CASE WHEN statut = "justifie" THEN 1 ELSE 0 END) as justifies')
            )
            ->groupBy('matieres.id', 'matieres.nom')
            ->when(!empty($params['date_debut']), function($q) use ($params) {
                $q->whereDate('presences.date_seance', '>=', $params['date_debut']);
            })
            ->when(!empty($params['date_fin']), function($q) use ($params) {
                $q->whereDate('presences.date_seance', '<=', $params['date_fin']);
            })
            ->get();
    }
    
    /**
     * Récupère les statistiques par classe
     *
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getStatsParClasse($params)
    {
        return DB::table('presences')
            ->join('classes', 'presences.classe_id', '=', 'classes.id')
            ->select(
                'classes.id',
                'classes.nom',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN statut = "present" THEN 1 ELSE 0 END) as presents'),
                DB::raw('SUM(CASE WHEN statut = "absent" THEN 1 ELSE 0 END) as absents'),
                DB::raw('SUM(CASE WHEN statut = "retard" THEN 1 ELSE 0 END) as retards'),
                DB::raw('SUM(CASE WHEN statut = "justifie" THEN 1 ELSE 0 END) as justifies')
            )
            ->groupBy('classes.id', 'classes.nom')
            ->when(!empty($params['date_debut']), function($q) use ($params) {
                $q->whereDate('presences.date_seance', '>=', $params['date_debut']);
            })
            ->when(!empty($params['date_fin']), function($q) use ($params) {
                $q->whereDate('presences.date_seance', '<=', $params['date_fin']);
            })
            ->get();
    }
    
    /**
     * Récupère les statistiques temporelles
     *
     * @param array $params
     * @return array
     */
    protected function getStatsTemporelles($params)
    {
        $startDate = !empty($params['date_debut']) 
            ? Carbon::parse($params['date_debut'])
            : now()->startOfMonth();
            
        $endDate = !empty($params['date_fin'])
            ? Carbon::parse($params['date_fin'])
            : now()->endOfMonth();
            
        $stats = [
            'par_jour' => [],
            'par_semaine' => [],
            'par_mois' => []
        ];
        
        // Statistiques par jour
        $stats['par_jour'] = DB::table('presences')
            ->select(
                DB::raw('DATE(date_seance) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN statut = "present" THEN 1 ELSE 0 END) as presents')
            )
            ->whereBetween('date_seance', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(date_seance)'))
            ->orderBy('date')
            ->get();
            
        // Statistiques par semaine
        $stats['par_semaine'] = DB::table('presences')
            ->select(
                DB::raw('YEAR(date_seance) as annee'),
                DB::raw('WEEK(date_seance, 1) as semaine'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN statut = "present" THEN 1 ELSE 0 END) as presents')
            )
            ->whereBetween('date_seance', [$startDate, $endDate])
            ->groupBy(DB::raw('YEAR(date_seance)'), DB::raw('WEEK(date_seance, 1)'))
            ->orderBy('annee')
            ->orderBy('semaine')
            ->get();
            
        // Statistiques par mois
        $stats['par_mois'] = DB::table('presences')
            ->select(
                DB::raw('YEAR(date_seance) as annee'),
                DB::raw('MONTH(date_seance) as mois'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN statut = "present" THEN 1 ELSE 0 END) as presents')
            )
            ->whereBetween('date_seance', [$startDate, $endDate])
            ->groupBy(DB::raw('YEAR(date_seance)'), DB::raw('MONTH(date_seance)'))
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();
            
        return $stats;
    }
    
    /**
     * Marque les présences pour une liste d'étudiants
     *
     * @param array $data
     * @param User $professeur
     * @return array
     */
    public function marquerPresences($data, $professeur)
    {
        DB::beginTransaction();
        
        try {
            $presences = [];
            $now = now();
            
            foreach ($data['etudiants'] as $etudiantId => $presenceData) {
                $presence = Presence::create([
                    'etudiant_id' => $etudiantId,
                    'matiere_id' => $data['matiere_id'],
                    'classe_id' => $data['classe_id'],
                    'professeur_id' => $professeur->id,
                    'date_seance' => $data['date_seance'],
                    'heure_debut' => $data['heure_debut'],
                    'heure_fin' => $data['heure_fin'],
                    'statut' => $presenceData['statut'],
                    'remarques' => $presenceData['remarques'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                
                $presences[] = $presence;
            }
            
            // Invalider les caches concernés
            $this->invalidateCache(['presences', 'statistiques'], ['presences', 'statistiques']);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Présences enregistrées avec succès.',
                'presences' => $presences
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement des présences: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Met à jour une présence existante
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updatePresence($id, $data)
    {
        $presence = Presence::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $presence->update([
                'statut' => $data['statut'],
                'remarques' => $data['remarques'] ?? null,
                'updated_at' => now(),
            ]);
            
            // Invalider les caches concernés
            $this->invalidateCache([
                'presences',
                'statistiques',
                'presence_' . $presence->id
            ], ['presences', 'statistiques']);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Présence mise à jour avec succès.',
                'presence' => $presence
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la présence: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Supprime une présence
     *
     * @param int $id
     * @return array
     */
    public function deletePresence($id)
    {
        $presence = Presence::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $presence->delete();
            
            // Invalider les caches concernés
            $this->invalidateCache([
                'presences',
                'statistiques',
                'presence_' . $presence->id
            ], ['presences', 'statistiques']);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Présence supprimée avec succès.'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression de la présence: ' . $e->getMessage()
            ];
        }
    }
}
