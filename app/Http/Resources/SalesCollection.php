<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SalesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $base = [
            'data' => SalesResource::collection($this->collection),
            'summary' => [
                'total_sales' => $this->collection->count(),
                'active_sales' => $this->collection->where('status', true)->count(),
                'inactive_sales' => $this->collection->where('status', false)->count(),
                'sales_with_area' => $this->collection->whereNotNull('kode_area')->count(),
                'sales_with_target' => $this->collection->where('target', '>', 0)->count(),
                'total_target' => $this->formatCurrency($this->collection->sum('target')),
                'total_target_raw' => (float) $this->collection->sum('target'),
                'average_target' => $this->formatCurrency($this->collection->avg('target')),
                'average_target_raw' => (float) $this->collection->avg('target'),
                'sales_with_phone' => $this->collection->whereNotNull('no_hp')->count(),
                'sales_with_address' => $this->collection->whereNotNull('alamat')->count(),
            ],
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'filters_applied' => [
                'search' => $request->get('search'),
                'area' => $request->get('area'),
                'status' => $request->get('status'),
                'sort' => $request->get('sort', 'kode_sales'),
                'direction' => $request->get('direction', 'asc'),
            ]
        ];

        // Add meta like CustomerCollection does to simplify tests
        $queryStartTime = $request->attributes->get('query_start_time', microtime(true));
        $base['api_version'] = '1.0';
        $base['timestamp'] = now()->toISOString();
        $base['query_time'] = round((microtime(true) - $queryStartTime) * 1000, 2) . 'ms';

        return $base;
    }

    /**
     * Format currency value
     */
    private function formatCurrency($value): string
    {
        if (is_null($value)) {
            return 'Rp 0';
        }
        
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }

    /**
     * Get additional data to be added to the response.
     */
    public function with(Request $request): array
    {
        $queryStartTime = $request->attributes->get('query_start_time', microtime(true));
        
        return [
            'api_version' => '1.0',
            'timestamp' => now()->toISOString(),
            'endpoint' => $request->url(),
            'query_time' => round((microtime(true) - $queryStartTime) * 1000, 2) . 'ms',
        ];
    }

    /**
     * Customize the pagination information for the resource.
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'pagination' => [
                'current_page' => $default['meta']['current_page'],
                'last_page' => $default['meta']['last_page'],
                'per_page' => $default['meta']['per_page'],
                'total' => $default['meta']['total'],
                'from' => $default['meta']['from'],
                'to' => $default['meta']['to'],
                'has_more_pages' => $default['meta']['current_page'] < $default['meta']['last_page'],
            ],
            'navigation' => [
                'first_page_url' => $default['links']['first'],
                'last_page_url' => $default['links']['last'],
                'prev_page_url' => $default['links']['prev'],
                'next_page_url' => $default['links']['next'],
            ]
        ];
    }
}
