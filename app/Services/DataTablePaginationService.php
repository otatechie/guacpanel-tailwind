<?php

namespace App\Services;

use Illuminate\Http\Request;

class DataTablePaginationService
{
    const DEFAULT_PAGE_SIZE = 10;
    const ALLOWED_PAGE_SIZES = [10, 25, 50];
    const ALLOW_ALL_OPTION = true;
    const MAX_ROWS_WHEN_ALL = 1000;

    public function resolvePage(Request $request): int
    {
        $pageRaw = $request->get('page');
        $page = is_numeric($pageRaw) ? (int) $pageRaw : 1;

        return max(1, $page);
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

        if ($allowAll && is_string($raw) && strtolower($raw) === 'all') {
            $total = is_int($filteredTotal) ? $filteredTotal : $allCap;

            return max(1, min($total, $allCap));
        }

        $value = is_numeric($raw) ? (int) $raw : $default;

        if (!in_array($value, $allowed, true)) {
            $value = $default;
        }

        return max(1, $value);
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
        $allParams = $request->all();
        $filters = [];

        foreach ($allParams as $key => $value) {
            if ($key !== 'page' && $key !== 'per_page') {
                $filters[$key] = $value;
            }
        }

        $filters['page'] = $request->get('page');
        $filters['per_page'] = $request->get('per_page');

        return $filters;
    }
}
