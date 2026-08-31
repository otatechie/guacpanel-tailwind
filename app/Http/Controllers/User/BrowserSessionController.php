<?php

namespace App\Http\Controllers\User;

use App\Traits\FormatsUserAgent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BrowserSessionController extends Controller
{
    use FormatsUserAgent;

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
                    'id' => $session->id ?? '',
                    'agent' => $this->formatAgent($session->user_agent ?? ''),
                    'ip' => $session->ip_address ?? '',
                    'lastActive' => $session->last_activity
                        ? Carbon::createFromTimestamp($session->last_activity)->diffForHumans()
                        : '',
                    'isCurrent' => ($session->id ?? '') === $request->session()->getId(),
                ];
            }
        }

        return Inertia::render('UserAccount/IndexSessionPage', [
            'user' => $user,
            'sessions' => $sessions,
        ]);
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
