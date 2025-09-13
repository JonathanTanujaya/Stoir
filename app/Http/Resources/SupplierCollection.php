<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SupplierCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => SupplierResource::collection($this->collection),
            'summary' => [
                'total_suppliers' => $this->collection->count(),
                'active_suppliers' => $this->collection->where('status', true)->count(),
                'inactive_suppliers' => $this->collection->where('status', false)->count(),
                'suppliers_with_address' => $this->collection->whereNotNull('alamat')->count(),
                'suppliers_with_phone' => $this->collection->whereNotNull('telp')->count(),
                'suppliers_with_contact' => $this->collection->whereNotNull('contact')->count(),
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
                'status' => $request->get('status'),
                'sort' => $request->get('sort', 'kode_supplier'),
                'direction' => $request->get('direction', 'asc'),
            ]
        ];
    }

    /**
     * Get additional data to be added to the response.
     */
    public function with(Request $request): array
    {
        $queryStartTime = $request->attributes->get('query_start_time', LARAVEL_START);
        
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
