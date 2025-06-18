<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class AdminSessionController extends Controller
{
    public function index(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return Inertia::render('Admin/IndexSessionPage', ['sessions' => []]);
        }

        $sessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('sessions.*', 'users.name', 'users.email')
            ->orderBy('sessions.last_activity', 'desc')
            ->paginate($request->input('per_page', 10))
            ->through(function ($session) {
                $agent = new Agent();
                $agent->setUserAgent($session->user_agent ?? '');

                return [
                    'id' => $session->id,
                    'user' => [
                        'name' => $session->name,
                        'email' => $session->email
                    ],
                    'device_info' => [
                        'device' => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
                        'platform' => $agent->platform() ?: 'Unknown',
                        'browser' => $agent->browser() ?: 'Unknown',
                    ],
                    'ip_address' => $session->ip_address,
                    'last_active_diff' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'is_current' => $session->id === request()->session()->getId(),
                ];
            });

        return Inertia::render('Admin/IndexSessionPage', ['sessions' => $sessions]);
    }


    public function destroy($sessionId)
    {
        if ($sessionId === request()->session()->getId()) {
            return back()->with('error', 'Cannot terminate current session');
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Session terminated successfully.');
    }


    public function destroyAllForUser($userId)
    {
        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', request()->session()->getId())
            ->delete();

        return back()->with('success', 'All sessions terminated successfully.');
    }
}
