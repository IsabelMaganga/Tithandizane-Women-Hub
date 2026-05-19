<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Mail, Notification, RateLimiter};

use App\Http\Controllers\Controller;
use App\Notifications\WelcomeNotification;

class AuthController extends Controller
{
    /**
     * Display the mentor login form.
     */
    public function showLogin()
    {
        return view('mentor.auth.login');
    }

    /**
     * Handle mentor login attempt.
     * Validates credentials and redirects on success/failure.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login - mentors table doesn't have a role column
        if (Auth::guard('mentor')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('mentor')->user();

            // Check if mentor is active
            if ($user->status !== 'active') {
                Auth::guard('mentor')->logout();
                return back()->withErrors([
                    'email' => 'Your account is ' . $user->status . '. Please contact administrator.',
                ])->onlyInput('email');
            }

            // Optional: Send welcome notification (consider moving this to a listener)
            Notification::send($user, new WelcomeNotification($user));

            return redirect()->intended(route('mentor.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Logout the current mentor and invalidate session.
     */
    public function logout(Request $request)
    {
        Auth::guard('mentor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mentor.login')->with('success', 'Logged out successfully.');
    }

    public function logoutAllSessions(Request $request)
    {
        try {
            DB::table('sessions')->where('user_id', Auth::id())->delete();

            Auth::guard('mentor')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('mentor.login')->with('success', 'Logged out successfully.');
        } catch (\Exception $e) {
            Log::error("Logout failed | Error: " . $e->getMessage());
            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ])->withInput();
        }
    }
}
