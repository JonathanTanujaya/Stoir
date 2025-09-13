<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReturPenerimaanDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => ReturPenerimaanDetailResource::collection($this->collection),
            'summary' => $this->generateSummary(),
        ];
    }

    /**
     * Generate summary statistics for the collection
     *
     * @return array<string, mixed>
     */
    private function generateSummary(): array
    {
        $total_qty = $this->collection->sum('qty_retur');
        $total_amount = $this->collection->sum(function ($item) {
            return $item->qty_retur * $item->harga_nett;
        });
        $unique_barang = $this->collection->unique('kode_barang')->count();
        $unique_penerimaan = $this->collection->unique('no_penerimaan')->count();
        $status_counts = $this->collection->groupBy('status')->map->count();

        return [
            'total_items' => $this->collection->count(),
            'total_qty_retur' => $total_qty,
            'total_amount' => $total_amount,
            'unique_barang_count' => $unique_barang,
            'unique_penerimaan_count' => $unique_penerimaan,
            'status_breakdown' => $status_counts,
            'average_price' => $total_qty > 0 ? round($total_amount / $total_qty, 2) : 0,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'generated_at' => now()->toISOString(),
                'total_records' => $this->collection->count(),
            ],
        ];
    }
}
