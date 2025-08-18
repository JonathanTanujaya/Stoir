<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FormatApiResponse
{
    /**
     * Handle an incoming request and format API responses consistently.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only format JSON responses for API requests
        if ($request->is('api/*') && $response instanceof JsonResponse) {
            $originalData = $response->getData(true);
            
            // If already formatted with success/error structure, return as is
            if (isset($originalData['success']) || isset($originalData['error'])) {
                return $response;
            }
            
            // Format the response according to StockFlow standards
            $formattedData = $this->formatResponse($originalData, $response->getStatusCode());
            
            $response->setData($formattedData);
        }

        return $response;
    }

    /**
     * Format response data according to StockFlow API standards
     */
    private function formatResponse($data, $statusCode): array
    {
        $isSuccess = $statusCode >= 200 && $statusCode < 300;
        
        if ($isSuccess) {
            return [
                'success' => true,
                'data' => $data,
                'message' => $this->getSuccessMessage($statusCode),
                'timestamp' => now()->toISOString(),
                'status_code' => $statusCode
            ];
        } else {
            return [
                'success' => false,
                'error' => $this->getErrorMessage($data, $statusCode),
                'message' => $this->getErrorMessage($data, $statusCode),
                'timestamp' => now()->toISOString(),
                'status_code' => $statusCode
            ];
        }
    }

    /**
     * Get appropriate success message based on status code
     */
    private function getSuccessMessage($statusCode): string
    {
        return match($statusCode) {
            200 => 'Request completed successfully',
            201 => 'Resource created successfully',
            202 => 'Request accepted',
            204 => 'Resource deleted successfully',
            default => 'Operation completed successfully'
        };
    }

    /**
     * Get appropriate error message based on data and status code
     */
    private function getErrorMessage($data, $statusCode): string
    {
        // If data contains error message, use it
        if (is_array($data) && isset($data['message'])) {
            return $data['message'];
        }
        
        if (is_string($data)) {
            return $data;
        }
        
        // Default error messages based on status code
        return match($statusCode) {
            400 => 'Bad request - invalid data provided',
            401 => 'Authentication required',
            403 => 'Access forbidden - insufficient permissions',
            404 => 'Resource not found',
            422 => 'Validation failed - please check your input',
            429 => 'Too many requests - please try again later',
            500 => 'Internal server error - please try again later',
            502 => 'Bad gateway - service temporarily unavailable',
            503 => 'Service unavailable - please try again later',
            default => 'An error occurred while processing your request'
        };
    }
}
