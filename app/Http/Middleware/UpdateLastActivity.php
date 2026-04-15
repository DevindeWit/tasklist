<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Only run if the user is logged in
        if (Auth::check()) {

            // 2. Define which HTTP methods count as "Activity"
            // This covers Create (POST), Update (PUT/PATCH), and Delete (DELETE)
            $activityMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

            if (in_array($request->method(), $activityMethods)) {
                $user = Auth::user();

                // 3. Update ONLY the authenticated user
                // We use saveQuietly to avoid triggering "User Updated" observers
                $user->forceFill([
                    'last_activity_at' => now(),
                ])->saveQuietly();
            }
        }

        return $next($request);
    }
}
