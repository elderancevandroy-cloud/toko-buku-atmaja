<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Searchable
{
    /**
     * Apply search filters to query
     */
    public function applySearch(Builder $query, Request $request, array $searchableFields = [])
    {
        // Global search
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchableFields, $searchTerm) {
                foreach ($searchableFields as $field) {
                    if (strpos($field, '.') !== false) {
                        // Relation field
                        [$relation, $relationField] = explode('.', $field);
                        $q->orWhereHas($relation, function ($relationQuery) use ($relationField, $searchTerm) {
                            $relationQuery->where($relationField, 'LIKE', "%{$searchTerm}%");
                        });
                    } else {
                        // Direct field
                        $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
                    }
                }
            });
        }

        // Date range search
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Status/Category filters
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        return $query;
    }

    /**
     * Get search statistics
     */
    public function getSearchStats(Builder $query, array $groupByFields = [])
    {
        $stats = [];
        
        foreach ($groupByFields as $field => $label) {
            $stats[$field] = [
                'label' => $label,
                'data' => $query->clone()
                    ->selectRaw("{$field}, COUNT(*) as count")
                    ->groupBy($field)
                    ->pluck('count', $field)
                    ->toArray()
            ];
        }

        return $stats;
    }
}