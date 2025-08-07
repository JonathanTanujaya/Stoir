<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize input data
        $input = $request->all();
        $sanitized = $this->sanitizeArray($input);
        
        // Replace request input with sanitized data
        $request->replace($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize array data
     */
    protected function sanitizeArray(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            $sanitizedKey = $this->sanitizeKey($key);
            
            if (is_array($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeString($value);
            } else {
                $sanitized[$sanitizedKey] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize array keys
     */
    protected function sanitizeKey(string $key): string
    {
        // Remove potentially dangerous characters from keys
        return preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $key);
    }

    /**
     * Sanitize string values
     */
    protected function sanitizeString(string $value): string
    {
        // Trim whitespace
        $value = trim($value);
        
        // Remove null bytes
        $value = str_replace("\0", '', $value);
        
        // Remove potential XSS attempts (basic)
        $value = strip_tags($value);
        
        // Remove potential SQL injection attempts
        $dangerous = [
            'UNION', 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 
            'CREATE', 'ALTER', 'EXEC', 'EXECUTE', 'SCRIPT', 'JAVASCRIPT'
        ];
        
        foreach ($dangerous as $keyword) {
            $value = preg_replace('/\b' . $keyword . '\b/i', '', $value);
        }
        
        return $value;
    }
}
