<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Attempt to login using the mentor guard
        // REMOVED the 'role' => 'mentor' condition since mentors table doesn't have a role column
        if (Auth::guard('mentor')->attempt($credentials)) {
            $request->session()->regenerate();
            
            $mentor = Auth::guard('mentor')->user();
            
            // Check if mentor is active
            if ($mentor->status !== 'active') {
                Auth::guard('mentor')->logout();
                return back()->withErrors([
                    'email' => 'Your account is ' . $mentor->status . '. Please contact administrator.',
                ])->onlyInput('email');
            }
            
            return redirect()->intended(route('mentor.dashboard'))
                ->with('success', 'Welcome back, ' . $mentor->name . '!');
        }

        // Check if mentor exists with this email
        $mentor = \App\Models\Mentor::where('email', $credentials['email'])->first();
        
        if (!$mentor) {
            return back()->withErrors([
                'email' => 'No account found with this email address.',
            ])->onlyInput('email');
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

        return redirect()->route('mentor.login')->with('success', 'Logged out successfully.');
    }

    public function logoutAllSessions(Request $request)
    {
        try {
            // Logout from all sessions
            Auth::guard('mentor')->logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('mentor.login')->with('success', 'Logged out from all devices successfully.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}