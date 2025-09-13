<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BarangCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => BarangResource::collection($this->collection),
            'summary' => [
                'total_items' => $this->collection->count(),
                'has_active_items' => $this->collection->where('status', true)->isNotEmpty(),
                'categories_count' => $this->collection->pluck('kode_kategori')->unique()->count(),
                'total_value' => $this->collection->sum('harga_jual'),
                'average_price' => $this->collection->avg('harga_jual'),
            ],
            'filters_applied' => [
                'search' => $request->get('search'),
                'kategori' => $request->get('kategori'),
                'status' => $request->get('status'),
                'lokasi' => $request->get('lokasi'),
            ]
        ];
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
                'current_page' => $default['meta']['current_page'] ?? 1,
                'per_page' => $default['meta']['per_page'] ?? 15,
                'total' => $default['meta']['total'] ?? 0,
                'last_page' => $default['meta']['last_page'] ?? 1,
                'has_more_pages' => $default['meta']['current_page'] < $default['meta']['last_page'],
                'showing' => sprintf(
                    'Showing %d to %d of %d results',
                    $default['meta']['from'] ?? 0,
                    $default['meta']['to'] ?? 0,
                    $default['meta']['total'] ?? 0
                ),
            ],
            'links' => [
                'first' => $default['links']['first'] ?? null,
                'last' => $default['links']['last'] ?? null,
                'prev' => $default['links']['prev'] ?? null,
                'next' => $default['links']['next'] ?? null,
            ]
        ];
    }
}
