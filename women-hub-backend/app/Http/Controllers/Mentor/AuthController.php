<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Using the User model
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

        // If your User model has a 'role' column, it's safer to include it here
        // to prevent Students from logging into the Mentor dashboard.
        $loginCredentials = array_merge($credentials, ['role' => 'mentor']);

        if (Auth::guard('mentor')->attempt($loginCredentials)) {
            $request->session()->regenerate();
            
            $user = Auth::guard('mentor')->user();
            
            // Check if user is active
            if ($user->status !== 'active') {
                Auth::guard('mentor')->logout();
                return back()->withErrors([
                    'email' => 'Your account is ' . $user->status . '. Please contact administrator.',
                ])->onlyInput('email');
            }
            
            return redirect()->intended(route('mentor.dashboard'))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Logic for specific error messaging
        $userExists = User::where('email', $credentials['email'])
            ->where('role', 'mentor')
            ->exists();
        
        if (!$userExists) {
            return back()->withErrors([
                'email' => 'No mentor account found with this email address.',
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
            // Note: This only logs out the current session. 
            // To truly logout ALL devices, you would use Auth::logoutOtherDevices($password)
            Auth::guard('mentor')->logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('mentor.login')->with('success', 'Logged out successfully.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}