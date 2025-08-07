<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Too many requests',
                'errors' => ['rate_limit' => ['Please try again later']],
                'retry_after' => $retryAfter
            ], 429)->header('Retry-After', $retryAfter);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::retriesLeft($key, $maxAttempts));
        $response->headers->set('X-RateLimit-Reset', RateLimiter::availableIn($key));

        return $response;
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            // Authenticated users: rate limit per user
            return 'api_rate_limit:user:' . $user->getFullIdentifierAttribute();
        }
        
        // Guest users: rate limit per IP
        return 'api_rate_limit:ip:' . $request->ip();
    }
}
