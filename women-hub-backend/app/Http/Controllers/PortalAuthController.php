<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('mentor')->check()) {
            return redirect()->route('mentor.dashboard');
        }

        return view('auth.portal-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        // Try admin guard first
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back!');
        }

        // Try mentor guard
        if (Auth::guard('mentor')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $mentor = Auth::guard('mentor')->user();

            if ($mentor->status !== 'active') {
                Auth::guard('mentor')->logout();
                return back()->withErrors([
                    'email' => 'Your account is ' . $mentor->status . '. Please contact the administrator.',
                ])->onlyInput('email');
            }

            return redirect()->intended(route('mentor.dashboard'))
                ->with('success', 'Welcome back, ' . $mentor->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
