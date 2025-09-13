<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PenerimaanFinanceDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'summary' => $this->when($this->collection->isNotEmpty(), [
                'total_items' => $this->collection->count(),
                'total_jumlah_invoice' => $this->collection->sum('jumlah_invoice'),
                'total_jumlah_bayar' => $this->collection->sum('jumlah_bayar'),
                'total_jumlah_dispensasi' => $this->collection->sum('jumlah_dispensasi'),
                'total_pembayaran' => $this->collection->sum(fn($item) => $item->jumlah_bayar + $item->jumlah_dispensasi),
                'total_sisa_tagihan' => $this->collection->sum(fn($item) => $item->jumlah_invoice - ($item->jumlah_bayar + $item->jumlah_dispensasi)),
                'average_jumlah_invoice' => round($this->collection->avg('jumlah_invoice'), 2),
                'average_jumlah_bayar' => round($this->collection->avg('jumlah_bayar'), 2),
                'status_breakdown' => $this->collection->groupBy('status')->map->count(),
                'invoice_stats' => [
                    'unique_invoices' => $this->collection->pluck('no_invoice')->unique()->count(),
                    'fully_paid' => $this->collection->filter(function ($item) {
                        return ($item->jumlah_bayar + $item->jumlah_dispensasi) >= $item->jumlah_invoice;
                    })->count(),
                    'partially_paid' => $this->collection->filter(function ($item) {
                        $totalPaid = $item->jumlah_bayar + $item->jumlah_dispensasi;
                        return $totalPaid > 0 && $totalPaid < $item->jumlah_invoice;
                    })->count(),
                    'unpaid' => $this->collection->filter(function ($item) {
                        return ($item->jumlah_bayar + $item->jumlah_dispensasi) == 0;
                    })->count(),
                ],
            ]),
        ];
    }
}


