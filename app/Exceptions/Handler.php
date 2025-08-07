<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle API exceptions
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions with consistent JSON responses
     */
    protected function handleApiException(Throwable $e, Request $request): JsonResponse
    {
        // Validation Exception
        if ($e instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // Authentication Exception
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
                'errors' => ['auth' => ['Please login to access this resource']]
            ], 401);
        }

        // Authorization Exception
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden',
                'errors' => ['authorization' => ['You do not have permission to access this resource']]
            ], 403);
        }

        // Model Not Found Exception
        if ($e instanceof ModelNotFoundException) {
            $model = class_basename($e->getModel());
            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found',
                'errors' => ['resource' => ["{$model} not found"]]
            ], 404);
        }

        // Not Found Exception
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Endpoint not found',
                'errors' => ['endpoint' => ['The requested endpoint does not exist']]
            ], 404);
        }

        // Method Not Allowed Exception
        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Method not allowed',
                'errors' => ['method' => ['The HTTP method is not allowed for this endpoint']]
            ], 405);
        }

        // Database Query Exception
        if ($e instanceof QueryException) {
            $message = 'Database error occurred';
            $errors = ['database' => ['A database error occurred']];

            // Provide more specific messages for common database errors
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $message = 'Duplicate entry detected';
                $errors = ['database' => ['This record already exists']];
            } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                $message = 'Foreign key constraint violation';
                $errors = ['database' => ['Cannot delete record due to existing references']];
            } elseif (str_contains($e->getMessage(), 'Data too long')) {
                $message = 'Data too long for field';
                $errors = ['database' => ['One or more fields contain too much data']];
            }

            // Log the actual error for debugging
            logger()->error('Database Error: ' . $e->getMessage(), [
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $errors
            ], 422);
        }

        // Generic Server Error
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        
        // Don't expose internal errors in production
        $message = 'An unexpected error occurred';
        $errors = ['system' => ['Please try again later']];

        if (config('app.debug')) {
            $message = $e->getMessage();
            $errors = [
                'system' => [$e->getMessage()],
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString())
            ];
        }

        // Log the error
        logger()->error('API Error: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'request' => $request->all(),
            'user' => $request->user()?->getFullIdentifierAttribute() ?? 'guest'
        ]);

        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
}
