<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;

class DataTableService
{
    const DEFAULT_PAGE_SIZE = 10;
    const ALLOWED_PAGE_SIZES = [10, 25, 50];
    const ALLOW_ALL_OPTION = true;
    const MAX_ROWS_WHEN_ALL = 1000;
    const SORT_DIRECTIONS = ['asc', 'desc'];
    const DEFAULT_SORT_DIRECTION = 'desc';


    public function applySearch(Builder $query, Request $request, array $searchableColumns): Builder
    {
        $search = $this->getRequestValue($request, 'search');

        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $this->applySearchToColumn($q, $column, $search);
            }
        });
    }


    public function applyFilters(Builder $query, Request $request, array $filterConfig): Builder
    {
        foreach ($filterConfig as $filterKey => $config) {
            $value = $this->getRequestValue($request, $filterKey);

            if (!$value) {
                continue;
            }

            $this->applyFilter($query, $config, $filterKey, $value);
        }

        return $query;
    }


    public function applySorting(Builder $query, Request $request, array $sortConfig): Builder
    {
        $sortField = $request->get('sort_by');

        if (!$sortField || !isset($sortConfig[$sortField])) {
            return $query;
        }

        $sortDirection = $this->normalizeSortDirection($request->get('sort_dir', self::DEFAULT_SORT_DIRECTION));
        $config = $sortConfig[$sortField];
        $type = $config['type'] ?? 'simple';

        return $this->applySortByType($query, $type, $config, $sortField, $sortDirection);
    }


    public function resolvePage(Request $request): int
    {
        return $this->normalizePositiveInteger($request->get('page'), 1);
    }


    public function resolvePerPage(
        Request $request,
        string $resourceName,
        int $default = 25,
        array $allowed = [10, 25, 50],
        bool $allowAll = true,
        int $allCap = 1000,
        ?int $filteredTotal = null
    ): int {
        $raw = $request->get('per_page');

        if ($this->isAllOption($raw, $allowAll)) {
            return $this->calculateAllPageSize($filteredTotal, $allCap);
        }

        return $this->normalizePageSize($raw, $default, $allowed);
    }


    public function resolvePerPageWithDefaults(Request $request, string $resourceName, ?int $filteredTotal = null): int
    {
        return $this->resolvePerPage(
            $request,
            $resourceName,
            self::DEFAULT_PAGE_SIZE,
            self::ALLOWED_PAGE_SIZES,
            self::ALLOW_ALL_OPTION,
            self::MAX_ROWS_WHEN_ALL,
            $filteredTotal
        );
    }


    public function buildFilters(Request $request): array
    {
        return $request->all();
    }


    public function applyAll(
        Builder $query,
        Request $request,
        array $searchableColumns = [],
        array $sortConfig = [],
        string $resourceName = 'items'
    ): array {
        if (!empty($searchableColumns)) {
            $query = $this->applySearch($query, $request, $searchableColumns);
        }

        if (!empty($sortConfig)) {
            $query = $this->applySorting($query, $request, $sortConfig);
        }

        $filteredTotal = $query->count();
        $perPage = $this->resolvePerPageWithDefaults($request, $resourceName, $filteredTotal);

        return [
            'query' => $query,
            'perPage' => $perPage,
            'filteredTotal' => $filteredTotal,
        ];
    }


    public function process(Builder|QueryBuilder $query, Request $request, array $config): array
    {
        if (!empty($config['searchable'])) {
            $query = $this->applySearch($query, $request, $config['searchable']);
        }

        if (!empty($config['sortable'])) {
            $query = $this->applySorting($query, $request, $config['sortable']);
        }

        $filteredTotal = $query->count();
        $resourceName = $config['resource'] ?? 'items';
        $perPage = $this->resolvePerPageWithDefaults($request, $resourceName, $filteredTotal);

        $paginator = $query->paginate($perPage)->withQueryString();

        if (isset($config['transform']) && is_callable($config['transform'])) {
            $paginator->through($config['transform']);
        }

        return [
            'data' => $paginator,
            'filters' => $this->buildFilters($request),
        ];
    }


    private function getRequestValue(Request $request, string $key): mixed
    {
        if (!$request->has($key)) {
            return null;
        }

        $value = $request->$key;

        return ($value === '' || $value === null) ? null : $value;
    }


    private function applySearchToColumn(Builder $query, string $column, string $search): void
    {
        if (str_contains($column, '.')) {
            $this->applyRelationshipSearch($query, $column, $search);
        } else {
            $query->orWhere($column, 'like', '%' . $search . '%');
        }
    }


    private function applyRelationshipSearch(Builder $query, string $column, string $search): void
    {
        [$relation, $field] = explode('.', $column, 2);

        $query->orWhereHas($relation, function ($subQuery) use ($field, $search) {
            $subQuery->where($field, 'like', '%' . $search . '%');
        });
    }


    private function applyFilter(Builder $query, array $config, string $filterKey, mixed $value): void
    {
        $type = $config['type'] ?? 'simple';

        match ($type) {
            'relationship' => $this->applyRelationshipFilter($query, $config, $value),
            'composite' => call_user_func($config['callback'], $query, $value),
            default => $query->where($filterKey, 'like', '%' . $value . '%'),
        };
    }


    private function applyRelationshipFilter(Builder $query, array $config, mixed $value): void
    {
        $query->whereHas($config['relation'], function ($q) use ($config, $value) {
            $q->where($config['field'], 'like', '%' . $value . '%');
        });
    }


    private function applySortByType(Builder $query, string $type, array $config, string $sortField, string $sortDirection): Builder
    {
        match ($type) {
            'relationship' => $this->applyRelationshipSort($query, $config, $sortDirection),
            'composite' => call_user_func($config['callback'], $query, $sortDirection),
            default => $query->orderBy($sortField, $sortDirection),
        };

        return $query;
    }


    private function applyRelationshipSort(Builder $query, array $config, string $sortDirection): void
    {
        $query->leftJoin($config['table'], $config['foreign_key'], '=', $config['local_key'])
            ->select($query->getModel()->getTable() . '.*')
            ->distinct()
            ->orderBy($config['order_by'], $sortDirection);
    }


    private function normalizeSortDirection(mixed $direction): string
    {
        return in_array($direction, self::SORT_DIRECTIONS, true) ? $direction : self::DEFAULT_SORT_DIRECTION;
    }


    private function normalizePositiveInteger(mixed $value, int $min = 1): int
    {
        $int = is_numeric($value) ? (int) $value : $min;

        return max($min, $int);
    }


    private function isAllOption(mixed $value, bool $allowAll): bool
    {
        return $allowAll && is_string($value) && strtolower($value) === 'all';
    }


    private function calculateAllPageSize(?int $filteredTotal, int $allCap): int
    {
        $total = is_int($filteredTotal) ? $filteredTotal : $allCap;

        return max(1, min($total, $allCap));
    }


    private function normalizePageSize(mixed $raw, int $default, array $allowed): int
    {
        $value = is_numeric($raw) ? (int) $raw : $default;

        if (!in_array($value, $allowed, true)) {
            $value = $default;
        }

        return max(1, $value);
    }
}
