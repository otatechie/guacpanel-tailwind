<?php

namespace App\Services;

use Illuminate\Http\Request;

class DataTablePaginationService
{
    /**
     * Default configuration constants
     */
    public const DEFAULT_PAGE_SIZE = 10;
    public const ALLOWED_PAGE_SIZES = [10, 25, 50];
    public const ALLOW_ALL_OPTION = true;
    public const MAX_ROWS_WHEN_ALL = 1000;

    /**
     * Resolve current page from request with safety.
     */
    public function resolvePage(Request $request): int
    {
        $pageRaw = $request->get('page');
        $page = is_numeric($pageRaw) ? (int) $pageRaw : 1;
        return max(1, $page);
    }

    /**
     * Resolve per-page value from request with policy.
     *
     * @param Request $request
     * @param string $resourceName Logical name for config/telemetry (e.g., 'suppliers')
     * @param int $default Default page size when none provided or invalid
     * @param array<int> $allowed Allowed numeric sizes
     * @param bool $allowAll Whether the special 'all' option is permitted
     * @param int $allCap Maximum allowed rows when 'all' is requested
     * @param int|null $filteredTotal Optional filtered total to honor for 'all'
     * @return int
     */
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
            // Use filtered total if provided; otherwise fall back to cap
            $total = is_int($filteredTotal) ? $filteredTotal : $allCap;
            return max(1, min($total, $allCap));
        }

        // Numeric handling
        $value = is_numeric($raw) ? (int) $raw : $default;

        // Clamp to allowed list
        if (!in_array($value, $allowed, true)) {
            $value = $default;
        }

        // Safety: ensure positive
        return max(1, $value);
    }

    /**
     * Resolve per-page with default configuration for most use cases.
     */
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

    /**
     * Build a standard filters array for Inertia views.
     *
     * @return array{per_page:mixed,page:mixed}
     */
    public function buildFilters(Request $request): array
    {
        return [
            'per_page' => $request->get('per_page'),
            'page' => $request->get('page'),
        ];
    }
}
