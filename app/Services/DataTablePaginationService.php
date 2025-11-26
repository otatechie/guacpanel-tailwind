<?php

namespace App\Services;

use Illuminate\Http\Request;

class DataTablePaginationService
{
    public const DEFAULT_PAGE_SIZE = 10;
    public const ALLOWED_PAGE_SIZES = [10, 25, 50];
    public const ALLOW_ALL_OPTION = true;
    public const MAX_ROWS_WHEN_ALL = 1000;

    public function resolvePerPage(
        Request $request,
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

    public function resolvePerPageWithDefaults(Request $request, ?int $filteredTotal = null): int
    {
        return $this->resolvePerPage(
            $request,
            self::DEFAULT_PAGE_SIZE,
            self::ALLOWED_PAGE_SIZES,
            self::ALLOW_ALL_OPTION,
            self::MAX_ROWS_WHEN_ALL,
            $filteredTotal
        );
    }

    /**
     * @return array{per_page:mixed,page:mixed}
     */
    public function buildFilters(Request $request): array
    {
        return [
            'per_page' => $request->get('per_page'),
            'page'     => $request->get('page'),
        ];
    }
}
