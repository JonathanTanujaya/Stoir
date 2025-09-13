<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PartPenerimaanDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($detail) {
                return new PartPenerimaanDetailResource($detail);
            }),
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'has_more_pages' => $this->hasMorePages(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
            'summary' => [
                'total_items' => $this->collection->count(),
                'total_quantity' => $this->collection->sum('qty_supply'),
                'total_gross_amount' => $this->collection->sum(function ($item) {
                    return $item->qty_supply * $item->harga;
                }),
                'total_net_amount' => $this->collection->sum('harga_nett'),
                'average_unit_price' => $this->collection->avg('harga'),
                'total_discount_amount' => $this->collection->sum(function ($item) {
                    return ($item->qty_supply * $item->harga) - $item->harga_nett;
                }),
            ],
            'constraints' => [
                'note' => 'This table has no primary key defined in database schema',
                'limitations' => [
                    'Individual record updates not supported',
                    'Individual record deletions not supported',
                    'Use bulk operations for modifications'
                ],
                'available_operations' => [
                    'index' => 'List all details for a penerimaan',
                    'store' => 'Create new detail (if kode_barang not exists)',
                    'stats' => 'Get aggregated statistics',
                    'bulk_delete' => 'Delete all details for a penerimaan'
                ]
            ]
        ];
    }

    /**
     * Additional data to include with the resource collection.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Part penerimaan details retrieved successfully',
        ];
    }
}
