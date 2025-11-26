<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;

class AdminSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-sessions');
    }

    public function index(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return Inertia::render('Admin/IndexSessionPage', ['sessions' => []]);
        }

        $currentSessionId = request()->session()->getId();

        $sessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select([
                'sessions.id',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.id as user_id',
                'users.name',
                'users.email',
            ])
            ->orderBy('sessions.last_activity', 'desc')
            ->paginate($request->input('per_page', 10))
            ->through(function ($session) use ($currentSessionId) {
                $agent = new Agent();
                $agent->setUserAgent($session->user_agent ?? '');

                return [
                    'id'   => $session->id,
                    'user' => [
                        'id'    => $session->user_id,
                        'name'  => $session->name,
                        'email' => $session->email,
                    ],
                    'device_info' => [
                        'device'   => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
                        'platform' => $agent->platform() ?: 'Unknown',
                        'browser'  => $agent->browser() ?: 'Unknown',
                    ],
                    'last_active_diff' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'is_current'       => $session->id === $currentSessionId,
                ];
            });

        return Inertia::render('Admin/IndexSessionPage', ['sessions' => $sessions]);
    }

    public function destroy($sessionId)
    {
        if ($sessionId === request()->session()->getId()) {
            session()->flash('error', 'Cannot terminate current session');

            return redirect()->back();
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        session()->flash('success', 'Session terminated successfully.');

        return redirect()->back();
    }

    public function destroyAllForUser($userId)
    {
        $currentSessionId = request()->session()->getId();

        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        session()->flash('success', 'All sessions terminated successfully.');

        return redirect()->back();
    }
}
