<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * OOP Concept: Single Responsibility Principle
 *   → This middleware ONLY checks if the logged-in user has the correct role.
 *
 * Usage in routes:
 *   Route::middleware('role:admin') → only admin can access
 *   Route::middleware('role:teacher') → only teacher can access
 *   Route::middleware('role:student') → only student can access
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request   The incoming HTTP request
     * @param  Closure  $next      The next middleware or controller
     * @param  string   $role      The required role (admin/teacher/student)
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check 1: Is the user logged in?
        // Check 2: Does the user's role match the required role?
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        if (!Auth::check() || $currentUser === null || $currentUser->role !== $role) {

            // Log unauthorized access attempts for security monitoring
            Log::info('Unauthorized access attempt blocked', [
                'user_id'       => Auth::id() ?? 'guest',
                'required_role' => $role,
                'url'           => $request->url(),
            ]);

            // Return 403 Forbidden error
            abort(403, 'Access denied. You do not have permission to view this page.');
        }

        // User has the correct role — continue to the next step
        return $next($request);
    }
}
