<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Traits\AppNotificationsHelperTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppNotificationPageController extends Controller
{
    use AppNotificationsHelperTrait;

    /**
     * The columns the table may order by, mapped to the trait's sort values.
     *
     * The list is a whitelist, not a convenience: the ordering ends up in an
     * orderByRaw, so nothing outside it may reach the query.
     */
    private const SORTABLE = ['title', 'scope', 'type', 'read', 'dismissed', 'created_at'];

    public function index(Request $request): Response
    {
        [$sortBy, $sortDir] = $this->sortFromRequest($request);

        $filters = [
            'scope' => (string) $request->query('scope', 'all'),

            /*
             * One control with three states, replacing a Read select and a
             * Dismissed select that together offered nine combinations, most of
             * them meaningless ("read and dismissed").
             *
             * Each label means exactly what it says: All is everything, Unread
             * excludes what has been read or dismissed, Dismissed is only what
             * was dismissed. Dismissing is visible because it drops the row out
             * of Unread and marks it in All, not because All quietly hides it.
             */
            'state' => $this->stateFromRequest($request),
            'search' => (string) $request->query('search', ''),

            // Echoed back so the table's own toolbar can seed itself with the
            // query and ordering that produced the rows on screen.
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'per_page' => $this->perPageFromRequest($request),
        ];

        return Inertia::render('Notifications/NotificationsIndex', [
            'filters' => $filters,
            'notifications' => fn() => $this->resolveNotifications(
                $request,
                $filters['per_page'],
                $this->queryFilters($filters),
            ),
        ]);
    }

    private function stateFromRequest(Request $request): string
    {
        $state = (string) $request->query('state', 'all');

        return in_array($state, ['unread', 'all', 'dismissed'], true) ? $state : 'all';
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function sortFromRequest(Request $request): array
    {
        $sortBy = (string) $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir') === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, self::SORTABLE, true)) {
            $sortBy = 'created_at';
        }

        return [$sortBy, $sortDir];
    }

    private function perPageFromRequest(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 25);

        return in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
    }

    /**
     * Translate the page's filters into the ones the listing query understands.
     */
    private function queryFilters(array $filters): array
    {
        $state = $filters['state'];

        return [
            'scope' => $filters['scope'],
            'read' => $state === 'unread' ? 'unread' : 'all',
            'dismissed' => match ($state) {
                'dismissed' => 'dismissed',
                'unread' => 'undismissed',
                default => 'all',
            },
            'type' => 'all',
            'search' => $filters['search'],
            'sort' => $this->sortValue($filters['sort_by'], $filters['sort_dir']),
            'per_page' => $filters['per_page'],
        ];
    }

    /**
     * `newest`/`oldest` are the date column's two directions under the names
     * they have always had, so existing links keep meaning what they meant.
     */
    private function sortValue(string $sortBy, string $sortDir): string
    {
        if ($sortBy === 'created_at') {
            return $sortDir === 'asc' ? 'oldest' : 'newest';
        }

        return "{$sortBy}_{$sortDir}";
    }
}
