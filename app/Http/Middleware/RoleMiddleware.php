<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin has access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check specific role
        switch ($role) {
            case 'admin':
                if (!$user->isAdmin()) {
                    abort(403, 'Access denied. Admin privileges required.');
                }
                break;
            case 'manager':
                if (!$user->isManager() && !$user->isAdmin()) {
                    abort(403, 'Access denied. Manager privileges required.');
                }
                break;
            case 'employee':
                if (!$user->isEmployee() && !$user->isManager() && !$user->isAdmin()) {
                    abort(403, 'Access denied.');
                }
                break;
            default:
                abort(403, 'Invalid role specified.');
        }

        return $next($request);
    }
}
