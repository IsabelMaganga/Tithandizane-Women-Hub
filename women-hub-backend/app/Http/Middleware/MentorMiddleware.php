<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MentorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // role based authorization
        if(!Auth::guard('mentor')->check()){
            // If user is authenticated as admin, redirect to admin dashboard
            if(Auth::guard('admin')->check()){
                return redirect('/admin/dashboard');
            }
            // Otherwise redirect to mentor login
            return redirect('/mentor/login');
        }

        return $next($request);
    }
}
