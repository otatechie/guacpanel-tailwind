<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Session;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Services\DataTableService;
use App\Http\Controllers\Controller;

class AdminSessionController extends Controller
{
    public function __construct(private DataTableService $dataTable)
    {
        $this->middleware('permission:view-sessions');
    }

    public function index(Request $request): Response
    {
        if (config('session.driver') !== 'database') {
            return Inertia::render('Admin/IndexSessionPage', [
                'sessions' => [],
                'filters' => $this->dataTable->buildFilters($request),
            ]);
        }

        $currentSessionId = request()->session()->getId();

        $result = $this->dataTable->process(
            query: Session::query()
                ->with('user:id,name,email')
                ->select(['id', 'user_id', 'user_agent', 'last_activity']),
            request: $request,
            config: [
                'searchable' => ['user.name', 'user.email'],
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
        ]);
    }

    public function destroy($sessionId)
    {
        if ($sessionId === request()->session()->getId()) {
            session()->flash('error', 'Cannot terminate current session');

            return redirect()->back();
        }

        Session::where('id', $sessionId)->delete();

        session()->flash('success', 'Session terminated successfully.');

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
            'last_active_diff' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            'is_current' => $session->id === $currentSessionId,
        ];
    }
}
