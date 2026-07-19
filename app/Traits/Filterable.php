<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Scope a query to apply filters from request.
     */
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                foreach ($this->getSearchableColumns() as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        foreach ($this->getFilterableColumns() as $column => $filter) {
            if ($request->filled($column)) {
                if (is_callable($filter)) {
                    $filter($query, $request->get($column));
                } else {
                    $query->where($column, $request->get($column));
                }
            }
        }

        return $query;
    }

    /**
     * Get searchable columns for the model.
     */
    protected function getSearchableColumns(): array
    {
        return $this->searchable ?? ['name'];
    }

    /**
     * Get filterable columns for the model.
     */
    protected function getFilterableColumns(): array
    {
        return $this->filterable ?? [];
    }
}
