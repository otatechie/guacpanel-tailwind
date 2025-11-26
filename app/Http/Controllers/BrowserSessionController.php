<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class BrowserSessionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $sessions = [];

        if (config('session.driver') === 'database') {
            $sessionRecords = DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get();

            foreach ($sessionRecords as $session) {
                $sessions[] = [
                    'id'         => $session->id ?? '',
                    'agent'      => $this->formatAgent($session->user_agent ?? ''),
                    'lastActive' => $session->last_activity ? Carbon::createFromTimestamp($session->last_activity)->diffForHumans() : '',
                    'isCurrent'  => ($session->id ?? '') === $request->session()->getId(),
                ];
            }
        }

        return Inertia::render('UserAccount/IndexSessionPage', [
            'user'     => $user,
            'sessions' => $sessions,
        ]);
    }

    protected function formatAgent($userAgent)
    {
        if (empty($userAgent)) {
            return ['device' => 'Unknown', 'browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        return [
            'device'   => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
            'platform' => $agent->platform() ?: 'Unknown',
            'browser'  => $agent->browser() ?: 'Unknown',
        ];
    }

    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        Auth::logoutOtherDevices($request->password);

        $this->deleteOtherSessionsFromDatabase($request);

        return back()->with('status', 'All other sessions have been terminated successfully.');
    }

    public function destroySession(Request $request, $sessionId)
    {
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->where('id', $sessionId)
                ->delete();
        }

        return back()->with('status', 'Session terminated successfully.');
    }

    private function deleteOtherSessionsFromDatabase(Request $request)
    {
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }
    }
}
