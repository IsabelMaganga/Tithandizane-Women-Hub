<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // role based authorization
        if(!Auth::guard('admin')->check()){
            // If user is authenticated as mentor, redirect to mentor dashboard
            if(Auth::guard('mentor')->check()){
                return redirect('/mentor/dashboard');
            }
            // Otherwise redirect to admin login
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
