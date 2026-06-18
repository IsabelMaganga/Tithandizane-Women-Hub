<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMentorApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isMentor()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Mentor access required.',
            ], 403);
        }

        return $next($request);
    }
}
