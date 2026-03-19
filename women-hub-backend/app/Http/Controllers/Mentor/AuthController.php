<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('mentor.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('mentor')->attempt([ ...$credentials, 'role' => 'mentor'])) {
            $request->session()->regenerate();

            // session(['auth_guard' => 'mentor']);

            return redirect()->intended(route('mentor.dashboard'))->with('success', 'Welcome back'. $credentials['email']);
        }

         // check if user exist but with wrong role
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user && !$user->isMentor()) {
            return back()->withErrors([
                'email' => 'This account does not have mentor privileges.',
            ]);
        }


        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('mentor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mentor.login')->with('success', 'logout successfully.');
    }

     public function logoutAllSessions(Request $request)
     {
        try {
            DB::table('sessions')->where('user_id', Auth::id())->delete();

            Auth::guard('mentor')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('mentor.login')->with('success', 'logout successfully.');

        } catch (\Exception $e) {
            // Log error with more details for debugging
            Log::error("logout failed for user ID: " . ($user->id ?? 'unknown') . " | Error: " . $e->getMessage());

            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ])->withInput();
        }
    }
}
