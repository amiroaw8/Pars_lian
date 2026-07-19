<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SessionManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SessionController extends Controller
{
    public function __construct(
        private readonly SessionManager $sessions
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $limit = $this->sessions->maxActiveSessions();

        $this->sessions->pruneExpired($user->id);

        $sessions = DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'is_current' => $session->id === session()->getId(),
                ];
            });

        if ($sessions->count() <= $limit) {
            return Redirect::route('auth.dashboard');
        }

        return view('auth.sessions.limit', compact('sessions', 'limit'));
    }

    public function destroy(Request $request, string $id)
    {
        $user = Auth::user();

        $session = DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if ($session) {
            DB::table(config('session.table', 'sessions'))->where('id', $id)->delete();
        }

        if ($id === session()->getId()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::route('login')->with('success', 'نشست فعلی بسته شد.');
        }

        $limit = $this->sessions->maxActiveSessions();
        $count = $this->sessions->activeCountForUser($user->id);

        if ($count <= $limit) {
            return Redirect::route('auth.dashboard');
        }

        return Redirect::route('auth.sessions.limit')
            ->with('success', 'نشست با موفقیت بسته شد.');
    }
}
