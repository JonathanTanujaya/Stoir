<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log incoming request
        $this->logRequest($request, $startTime);

        $response = $next($request);

        // Log response
        $this->logResponse($request, $response, $startTime);

        return $response;
    }

    /**
     * Log incoming request
     */
    protected function logRequest(Request $request, float $startTime): void
    {
        $data = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user' => $request->user()?->getFullIdentifierAttribute() ?? 'guest',
            'timestamp' => date('Y-m-d H:i:s'),
            'request_id' => uniqid(),
        ];

        // Only log request body for specific methods and exclude sensitive data
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $body = $request->all();
            
            // Remove sensitive fields
            $sensitiveFields = ['password', 'password_confirmation', 'current_password', 'token'];
            foreach ($sensitiveFields as $field) {
                if (isset($body[$field])) {
                    $body[$field] = '[REDACTED]';
                }
            }
            
            $data['body'] = $body;
        }

        Log::channel('daily')->info('API Request', $data);
    }

    /**
     * Log response
     */
    protected function logResponse(Request $request, Response $response, float $startTime): void
    {
        $duration = round((microtime(true) - $startTime) * 1000, 2); // ms
        
        $data = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user' => $request->user()?->getFullIdentifierAttribute() ?? 'guest',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        // Log response body for errors or if debug mode
        if ($response->getStatusCode() >= 400 || config('app.debug')) {
            $content = $response->getContent();
            
            // Limit response size for logging
            if (strlen($content) > 1000) {
                $content = substr($content, 0, 1000) . '... [TRUNCATED]';
            }
            
            $data['response'] = $content;
        }

        $level = $response->getStatusCode() >= 500 ? 'error' : 
                ($response->getStatusCode() >= 400 ? 'warning' : 'info');

        Log::channel('daily')->$level('API Response', $data);
    }
}
