<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DivisiCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'data' => DivisiResource::collection($this->collection),
            'meta' => [
                'total' => $this->collection->count(),
            ],
            'summary' => [
                'total_divisi' => $this->collection->count(),
                'total_banks' => $this->collection->sum(function ($divisi) {
                    return isset($divisi->banks) ? $divisi->banks->count() : 0;
                }),
                'total_areas' => $this->collection->sum(function ($divisi) {
                    return isset($divisi->areas) ? $divisi->areas->count() : 0;
                }),
                'total_customers' => $this->collection->sum(function ($divisi) {
                    return isset($divisi->customers) ? $divisi->customers->count() : 0;
                }),
                'total_sales' => $this->collection->sum(function ($divisi) {
                    return isset($divisi->sales) ? $divisi->sales->count() : 0;
                }),
                'total_barangs' => $this->collection->sum(function ($divisi) {
                    return isset($divisi->barangs) ? $divisi->barangs->count() : 0;
                }),
                'total_invoices' => $this->collection->sum(function ($divisi) {
                    return isset($divisi->invoices) ? $divisi->invoices->count() : 0;
                }),
                'total_suppliers' => $this->collection->sum(function ($divisi) {
                    return isset($divisi->suppliers) ? $divisi->suppliers->count() : 0;
                }),
            ],
        ];
    }
}
