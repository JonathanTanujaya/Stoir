<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     */
    public $collects = CustomerResource::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'summary' => [
                'total_customers' => $this->collection->count(),
                'active_customers' => $this->collection->where('status', true)->count(),
                'inactive_customers' => $this->collection->where('status', false)->count(),
                'total_credit_limit' => $this->collection->sum('credit_limit'),
                'formatted_total_credit' => 'Rp ' . number_format($this->collection->sum('credit_limit'), 0, ',', '.'),
                'average_credit_limit' => $this->collection->avg('credit_limit'),
                'formatted_avg_credit' => 'Rp ' . number_format($this->collection->avg('credit_limit') ?: 0, 0, ',', '.')
            ],
            'filters_applied' => $this->getAppliedFilters($request),
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->hasMorePages(),
                'links' => [
                    'first' => $this->url(1),
                    'last' => $this->url($this->lastPage()),
                    'prev' => $this->previousPageUrl(),
                    'next' => $this->nextPageUrl(),
                ]
            ],
            'meta' => [
                'resource_type' => 'CustomerCollection',
                'api_version' => '1.0',
                'generated_at' => now()->toISOString(),
                'request_id' => $request->header('X-Request-ID', uniqid()),
            ],
            'api_version' => '1.0',
            'timestamp' => now()->toISOString(),
            'query_time' => $this->getQueryTime($request)
        ];
    }

    /**
     * Get the applied filters from the request.
     */
    private function getAppliedFilters(Request $request): array
    {
        $filters = [];
        
        if ($request->filled('search')) {
            $filters['search'] = $request->get('search');
        }
        
        if ($request->filled('area')) {
            $filters['area'] = $request->get('area');
        }
        
        if ($request->filled('sales')) {
            $filters['sales'] = $request->get('sales');
        }
        
        if ($request->filled('status')) {
            $filters['status'] = $request->get('status');
        }
        
        if ($request->filled('min_credit')) {
            $filters['min_credit'] = $request->get('min_credit');
        }
        
        if ($request->filled('max_credit')) {
            $filters['max_credit'] = $request->get('max_credit');
        }
        
        if ($request->filled('sort')) {
            $filters['sort'] = $request->get('sort');
            $filters['direction'] = $request->get('direction', 'asc');
        }
        
        return $filters;
    }

    /**
     * Get query execution time.
     */
    private function getQueryTime(Request $request): string
    {
        $startTime = $request->attributes->get('query_start_time', microtime(true));
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        return number_format($executionTime, 2) . 'ms';
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'resource_type' => 'CustomerCollection',
                'api_version' => '1.0',
                'generated_at' => now()->toISOString(),
                'request_id' => $request->header('X-Request-ID', uniqid())
            ]
        ];
    }

    /**
     * Customize the response for a request.
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'CustomerCollection');
        $response->header('X-API-Version', '1.0');
        $response->header('X-Total-Count', $this->total());
        $response->header('X-Per-Page', $this->perPage());
        $response->header('X-Current-Page', $this->currentPage());
    }
}
