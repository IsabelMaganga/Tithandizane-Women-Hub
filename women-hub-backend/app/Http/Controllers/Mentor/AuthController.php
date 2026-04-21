<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Log, Mail, Notification, RateLimiter};
use App\Http\Controllers\Controller;
use App\Notifications\WelcomeNotification;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Handles authentication for mentors, including login, logout, password reset, and session management.
 */
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
     * Validates credentials, checks role, and redirects on success/failure.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login with mentor guard and role check
        if (Auth::guard('mentor')->attempt([ ...$credentials, 'role' => 'mentor'])) {
            $request->session()->regenerate();

            $mentor = Auth::guard('mentor')->user();
            $mentor->notify(new WelcomeNotification($mentor));

            return redirect()->intended(route('mentor.dashboard'))->with('success', 'Welcome back ' . $credentials['email']);
        }

         // Check if user exists but with wrong role
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

    /**
     * Logout the current mentor and invalidate session.
     */
    public function logout(Request $request)
    {
        Auth::guard('mentor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mentor.login')->with('success', 'logout successfully.');
    }

    /**
     * Logout mentor from all sessions by clearing session records.
     */
    public function logoutAllSessions(Request $request)
    {
        try {
            // Delete all sessions for this user
            DB::table('sessions')->where('user_id', Auth::id())->delete();

            Auth::guard('mentor')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('mentor.login')->with('success', 'logout successfully.');

        } catch (\Exception $e) {
            // Log error with more details for debugging
            Log::error("logout failed for user ID: " . (Auth::id() ?? 'unknown') . " | Error: " . $e->getMessage());

            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    /**
     * Display the forgot password form for mentors.
     */
    public function showForgot()
    {
        return view('mentor.auth.forgot');
    }

    /**
     * Display the password reset form with the provided token.
     */
    public function showVerify($token)
    {
        return view('mentor.auth.reset-password', ['token' => $token]);
    }

    /**
     * Send a password reset link to the mentor's email, with rate limiting and cooldown checks.
     */
    public function sendResetLink(Request $request)
    {
        try
        {
            // Rate limiting key based on email
            $key = Str::lower($request->input('email')).'|password-reset';

            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);

                return redirect()->back()->with([
                    'ManyAttempts' => 'Too many attempts. Please wait before trying again.',
                    'retry_after' => $seconds // pass seconds to the view
                ]);
            }

            // Record the attempt (expires in 5 minutes)
            RateLimiter::hit($key, 300);

            // Validate input
            $validated = $request->validate([
                'email' => 'required|email',
            ]);

            // Look up user by email
            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return redirect()->back()->with('email', 'Email does not exist in our records.');
            }

            if ($user->role === 'mentor') {

                // Check cooldown (10 hours since last password update)
                if ($user->last_password_updated_at &&
                    Carbon::parse($user->last_password_updated_at)->addHours(10)->isFuture()) {

                    $remaining = Carbon::parse($user->last_password_updated_at)
                        ->addHours(10)
                        ->diffForHumans(Carbon::now(), [
                            'parts' => 2,
                            'short' => true,
                        ]);

                    return redirect()->back()
                        ->with([
                            'remaining' => $remaining,
                    ]);

                }

                // Generate secure reset token
                $token = \Illuminate\Support\Str::random(60);

                // Store token in password_resets table
                Db::table('password_resets')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'email' => $user->email,
                        'token' => $token,
                        'created_at' => now(),
                    ]
                );

                // Send reset email
                Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject('Reset Your Password');
                });

                return redirect()->back()->with('success', 'A password reset link has been sent to your email address.');
            }

            return redirect()->back()->with('error', 'User exists but is not a mentor.');

        } catch (\Exception $e)
        {
            // Catch any unexpected errors
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Update the mentor's password using the reset token.
     * Validates token, checks expiry, and updates password with cooldown tracking.
     */
    public function updatePassword(Request $request){
       try {
            $validated = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string|min:8|confirmed',
                'token'    => 'required'
            ]);

            // Check if token exists in password_resets table
            $reset = DB::table('password_resets')
                ->where('email', $validated['email'])
                ->where('token', $validated['token'])
                ->first();

            if (!$reset) {
                return redirect()->back()->with('error', 'Invalid or expired reset token.');
            }

            // Expire after 2 minutes
            if (Carbon::parse($reset->created_at)->addMinutes(2)->isPast()) {
                return redirect()->back()->with('error', 'This reset link has expired. Please request a new one.');
            }

            // Find the user
            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                return redirect()->back()->withErrors(['email' => 'Email not found.']);
            }

            // Update password and track last update time
            $user->password = Hash::make($validated['password']);
            $user->last_password_updated_at = Carbon::now();
            $user->save();

            // Delete reset record so token cannot be reused
            DB::table('password_resets')->where('email', $validated['email'])->delete();

            return redirect()->route('mentor.login')->with('success', 'Your password has been updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'email' => '*',
                'password' => '*',
                'password_confirmation' => '*'
            ]);
        }
    }

}
