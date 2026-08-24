<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Services\DataTableService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Jenssegers\Agent\Agent;

class AdminSessionController extends Controller implements HasMiddleware
{
    public function __construct(private DataTableService $dataTable) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-sessions|manage-sessions'),
            new Middleware('permission:manage-sessions', only: ['destroy', 'destroyAllForUser']),
        ];
    }

    public function index(Request $request): Response
    {
        if (config('session.driver') !== 'database') {
            return Inertia::render('Admin/IndexSessionPage', [
                'sessions' => ['data' => [], 'current_page' => 1, 'per_page' => 10, 'total' => 0],
                'filters' => $this->dataTable->buildFilters($request),
                'driverSupported' => false,
            ]);
        }

        $currentSessionId = request()->session()->getId();

        $result = $this->dataTable->process(
            query: Session::query()
                ->with('user:id,name,email')
                ->select(['id', 'user_id', 'ip_address', 'user_agent', 'last_activity'])
                ->orderByDesc('last_activity'),
            request: $request,
            config: [
                'searchable' => ['user.name', 'user.email', 'ip_address'],
                'sortable' => [
                    'last_activity' => ['type' => 'simple'],
                    'user.name' => ['type' => 'relationship', 'relation' => 'user', 'column' => 'name'],
                    'user.email' => ['type' => 'relationship', 'relation' => 'user', 'column' => 'email'],
                ],
                'resource' => 'sessions',
                'transform' => fn($session) => $this->transformSession($session, $currentSessionId),
            ],
        );

        return Inertia::render('Admin/IndexSessionPage', [
            'sessions' => $result['data'],
            'filters' => $result['filters'],
            'driverSupported' => true,
        ]);
    }

    public function destroy($sessionId)
    {
        if ($sessionId === request()->session()->getId()) {
            session()->flash('error', 'You cannot sign out of the session you are using.');

            return redirect()->back();
        }

        Session::where('id', $sessionId)->delete();

        session()->flash('success', 'Signed out of that session.');

        return redirect()->back();
    }

    public function destroyAllForUser($userId)
    {
        $currentSessionId = request()->session()->getId();

        Session::where('user_id', $userId)->where('id', '!=', $currentSessionId)->delete();

        session()->flash('success', 'All sessions terminated successfully.');

        return redirect()->back();
    }

    private function transformSession(Session $session, string $currentSessionId): array
    {
        $agent = new Agent();
        $agent->setUserAgent($session->user_agent ?? '');

        return [
            'id' => $session->id,
            'user' => [
                'id' => $session->user?->id,
                'name' => $session->user?->name,
                'email' => $session->user?->email,
            ],
            'device_info' => [
                'device' => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
                'platform' => $agent->platform() ?: 'Unknown',
                'browser' => $agent->browser() ?: 'Unknown',
            ],
            'ip_address' => $session->ip_address,
            'last_active_diff' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            'last_active_exact' => Carbon::createFromTimestamp($session->last_activity)->toDayDateTimeString(),
            'is_current' => $session->id === $currentSessionId,
        ];
    }
}
