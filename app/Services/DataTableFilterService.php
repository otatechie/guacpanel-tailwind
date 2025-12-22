<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DataTableFilterService
{
    public function applySearch(Builder $query, Request $request, array $searchableColumns): Builder
    {
        if (!$request->has('search') || !$request->search) {
            return $query;
        }

        $search = $request->search;

        return $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                if (str_contains($column, '.')) {
                    [$relation, $field] = explode('.', $column);
                    $q->orWhereHas($relation, function ($subQuery) use ($field, $search) {
                        $subQuery->where($field, 'like', '%' . $search . '%');
                    });
                } else {
                    $q->orWhere($column, 'like', '%' . $search . '%');
                }
            }
        });
    }

    public function applyFilters(Builder $query, Request $request, array $filterConfig): Builder
    {
        foreach ($filterConfig as $filterKey => $config) {
            if (!$request->has($filterKey) || !$request->$filterKey) {
                continue;
            }

            $value = $request->$filterKey;

            if (isset($config['type']) && $config['type'] === 'relationship') {
                $query->whereHas($config['relation'], function ($q) use ($config, $value) {
                    $q->where($config['field'], 'like', '%' . $value . '%');
                });
            } elseif (isset($config['type']) && $config['type'] === 'composite') {
                call_user_func($config['callback'], $query, $value);
            } else {
                $query->where($filterKey, 'like', '%' . $value . '%');
            }
        }

        return $query;
    }

    public function applySorting(Builder $query, Request $request, array $sortConfig): Builder
    {
        $sortField = $request->get('sort_by');
        $sortDirection = $request->get('sort_dir', 'desc');
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        if (!$sortField || !isset($sortConfig[$sortField])) {
            return $query;
        }

        $config = $sortConfig[$sortField];

        if (isset($config['type']) && $config['type'] === 'relationship') {
            $query->join($config['table'], $config['foreign_key'], '=', $config['local_key'])
                ->select($query->getModel()->getTable() . '.*')
                ->orderBy($config['order_by'], $sortDirection);
        } elseif (isset($config['type']) && $config['type'] === 'composite') {
            call_user_func($config['callback'], $query, $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }
}
