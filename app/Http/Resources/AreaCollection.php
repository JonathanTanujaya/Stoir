<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AreaCollection extends ResourceCollection
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
            'data' => AreaResource::collection($collection),
            'summary' => [
                'total_areas' => $collection->count(),
                'active_areas' => $collection->filter(function ($item) {
                    return isset($item->status) ? (bool) $item->status : false;
                })->count(),
                'coverage_by_divisi' => $collection->groupBy(function ($item) {
                    return isset($item->kode_divisi) ? $item->kode_divisi : 'unknown';
                })->count(),
                'average_areas_per_divisi' => $collection->groupBy(function ($item) {
                    return isset($item->kode_divisi) ? $item->kode_divisi : 'unknown';
                })->count() > 0
                    ? round($collection->count() / $collection->groupBy(function ($item) {
                        return isset($item->kode_divisi) ? $item->kode_divisi : 'unknown';
                    })->count(), 2)
                    : 0,
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
            'divisi_breakdown' => $collection->groupBy(function ($item) {
                return isset($item->kode_divisi) ? $item->kode_divisi : 'unknown';
            })->map(function ($areas, $kodeDivisi) {
                return [
                    'kode_divisi' => $kodeDivisi,
                    'total' => $areas->count(),
                    'active' => $areas->filter(function ($item) {
                        return isset($item->status) ? (bool) $item->status : false;
                    })->count(),
                    'inactive' => $areas->filter(function ($item) {
                        return isset($item->status) ? !(bool) $item->status : false;
                    })->count(),
                ];
            })->values(),
        ];

        if ($isPaginated) {
            $base['pagination'] = [
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
                ],
            ];
            $base['filters_applied'] = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'sort' => $request->get('sort', $request->get('sort_by', 'kode_area')),
                'direction' => $request->get('direction', $request->get('sort_order', 'asc')),
            ];
        }

        return $base;
    }

    public function with($request): array
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
