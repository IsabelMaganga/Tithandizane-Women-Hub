<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Mail, RateLimiter};
use App\Models\Notification as AppNotification;

use App\Http\Controllers\Controller;
use App\Notifications\WelcomeNotification;

class AuthController extends Controller
{
    /**
     * Display the mentor login form.
     */
    public function showLogin()
    {
        // ✅ FIXED: render the view directly instead of redirecting.
        // The previous redirect()->route('portal.login') was killing flash
        // session data before the Blade template could read it.
        return view('auth.mentor-login');
    }

    /**
     * Handle mentor login attempt.
     * Validates credentials and redirects on success/failure.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login with credentials only — status is checked separately below
        if (Auth::guard('mentor')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('mentor')->user();

            // If the account is not active, log them back out immediately and
            // redirect with a session flag so the view can show the modal.
            if ($user->status !== 'active') {
                Auth::guard('mentor')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // ✅ FIXED: redirect directly to the login VIEW route (portal.login),
                // not to a route that itself redirects — that double-redirect
                // was swallowing the flash data before Blade could read it.
                return redirect()->route('portal.login')
                    ->with('account_inactive', true)
                    ->with('account_status', $user->status); // 'pending' | 'inactive'
            }

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

        return redirect()->route('portal.login')->with('success', 'Logged out successfully.');
    }

    public function logoutAllSessions(Request $request)
    {
        try {
            DB::table('sessions')->where('user_id', Auth::id())->delete();

            Auth::guard('mentor')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('portal.login')->with('success', 'Logged out successfully.');
        } catch (\Exception $e) {
            Log::error("Logout failed | Error: " . $e->getMessage());
            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ])->withInput();
        }
    }
}