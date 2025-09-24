<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class KategoriCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $collection = $this->collection;
        $isPaginated = method_exists($this->resource, 'total');

        $base = [
            'data' => KategoriResource::collection($collection),
            'summary' => [
                'total_kategoris' => $collection->count(),
                'active_kategoris' => $collection->filter(function ($item) {
                    return isset($item->status) ? (bool) $item->status : false;
                })->count(),
            ],
        ];

        $base['meta'] = [
            'total' => $collection->count(),
            'active_count' => $collection->filter(function ($item) {
                return isset($item->status) ? (bool) $item->status : false;
            })->count(),
            'inactive_count' => $collection->filter(function ($item) {
                return isset($item->status) ? !(bool) $item->status : false;
            })->count(),
        ];

        if ($isPaginated) {
            $base['pagination'] = [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ];
            $base['filters_applied'] = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'sort' => $request->get('sort', 'kode_kategori'),
                'direction' => $request->get('direction', 'asc'),
            ];
        }

        return $base;
    }

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
}
