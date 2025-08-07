<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleId): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required',
                'errors' => ['auth' => ['Please login to access this resource']]
            ], 401);
        }

        // Admin can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check specific module access
        if (!$user->hasModuleAccess($moduleId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Module access denied',
                'errors' => ['authorization' => ['You do not have access to this module']]
            ], 403);
        }

        return $next($request);
    }
}
