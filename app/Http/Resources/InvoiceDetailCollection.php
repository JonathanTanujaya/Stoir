<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => InvoiceDetailResource::collection($this->collection),
            'summary' => [
                'total_items' => $this->collection->count(),
                'total_quantity' => $this->collection->sum('qty_supply'),
                'total_gross_amount' => $this->collection->sum(function ($item) {
                    return $item->qty_supply * $item->harga_jual;
                }),
                'total_net_amount' => $this->collection->sum('harga_nett'),
                'average_unit_price' => $this->collection->avg('harga_jual'),
            ],
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'has_more_pages' => $this->currentPage() < $this->lastPage(),
            ],
            'navigation' => [
                'first_page_url' => $this->url(1),
                'last_page_url' => $this->url($this->lastPage()),
                'prev_page_url' => $this->previousPageUrl(),
                'next_page_url' => $this->nextPageUrl(),
            ],
            'filters_applied' => [
                'search' => $request->get('search'),
                'kode_barang' => $request->get('kode_barang'),
                'jenis' => $request->get('jenis'),
                'status' => $request->get('status'),
                'min_harga' => $request->get('min_harga'),
                'max_harga' => $request->get('max_harga'),
                'min_qty' => $request->get('min_qty'),
                'max_qty' => $request->get('max_qty'),
                'sort_by' => $request->get('sort_by', 'id'),
                'sort_order' => $request->get('sort_order', 'asc'),
            ],
        ];
    }

    /**
     * Additional data to include with the resource collection.
     *
     * @return array<string, mixed>
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
