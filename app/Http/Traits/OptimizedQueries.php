<?php

namespace App\Http\Traits;

trait OptimizedQueries
{
    /**
     * Récupère les données optimisées pour les requêtes fréquentes
     *
     * @param  mixed  $query
     * @param  array  $with
     * @return mixed
     */
    protected function optimizeQuery($query, array $with = [])
    {
        return $query->with($with)->select('*');
    }

    /**
     * Récupère les données paginées avec un nombre d'éléments par page configurable
     *
     * @param  mixed  $query
     * @param  int  $perPage
     * @param  array  $with
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginateWith($query, $perPage = 15, array $with = [])
    {
        return $this->optimizeQuery($query, $with)->paginate($perPage);
    }

    /**
     * Récupère les données pour les listes déroulantes
     *
     * @param  string  $modelClass
     * @param  string  $displayField
     * @param  string  $valueField
     * @param  array  $conditions
     * @return \Illuminate\Support\Collection
     */
    protected function getSelectOptions($modelClass, $displayField, $valueField = 'id', $conditions = [])
    {
        $query = $modelClass::query();
        
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }
        
        return $query->get()
                    ->pluck($displayField, $valueField);
    }

    /**
     * Récupère les statistiques de base pour un modèle
     *
     * @param  string  $modelClass
     * @param  array  $conditions
     * @return array
     */
    protected function getModelStats($modelClass, $conditions = [])
    {
        $query = $modelClass::query();
        
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }
        
        return [
            'total' => $query->count(),
            'today' => $query->whereDate('created_at', today())->count(),
            'this_week' => $query->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => $query->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
        ];
    }
}
