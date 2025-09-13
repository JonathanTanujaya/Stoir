<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'kode_divisi' => $this->kode_divisi,
            'kode_sales' => $this->kode_sales,
            'nama_sales' => $this->nama_sales,
            'kode_area' => $this->kode_area,
            'status' => isset($this->status) ? (bool) $this->status : null,
            'divisi' => $this->whenLoaded('divisi', function () {
                return [
                    'kode_divisi' => $this->divisi->kode_divisi,
                    'divisi' => $this->divisi->divisi ?? ($this->divisi->nama_divisi ?? null),
                    'status' => (bool) ($this->divisi->status ?? true),
                ];
            }),
            'area' => $this->whenLoaded('area', function () {
                return [
                    'kode_area' => $this->area->kode_area ?? null,
                    'area' => $this->area->area ?? ($this->area->nama_area ?? null),
                    'status' => isset($this->area->status) ? (bool) $this->area->status : null,
                ];
            }),
            'contact_info' => [
                'alamat' => $this->alamat,
                'no_hp' => $this->no_hp,
                'formatted_address' => $this->formatFullAddress(),
            ],
            'performance' => [
                'target' => $this->formatCurrency($this->target),
                'target_raw' => (float) $this->target,
                'status' => $this->status,
                'status_text' => $this->status ? 'Aktif' : 'Tidak Aktif',
            ],
            'statistics' => $this->when($this->relationLoaded('invoices'), function () {
                $invoices = $this->invoices;
                $totalInvoices = $invoices->count();
                $totalNilai = $invoices->sum('grand_total');
                $achievement = $this->target > 0 ? ($totalNilai / $this->target) * 100 : 0;
                
                return [
                    'total_invoices' => $totalInvoices,
                    'total_sales_value' => $this->formatCurrency($totalNilai),
                    'total_sales_raw' => (float) $totalNilai,
                    'achievement_percentage' => round($achievement, 2),
                    'achievement_formatted' => round($achievement, 2) . '%',
                    'average_invoice_value' => $totalInvoices > 0 ? $this->formatCurrency($totalNilai / $totalInvoices) : 'Rp 0',
                    'average_invoice_raw' => $totalInvoices > 0 ? (float) ($totalNilai / $totalInvoices) : 0,
                    'performance_status' => $achievement >= 100 ? 'Target Tercapai' : 'Belum Tercapai Target',
                    'last_sale_date' => $invoices->sortByDesc('tgl_invoice')->first()?->tgl_invoice?->format('Y-m-d')
                ];
            }),
            'metadata' => [
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
                'has_sales_activity' => $this->when($this->relationLoaded('invoices'), function () {
                    return $this->invoices->isNotEmpty();
                }),
                'has_area_assignment' => !is_null($this->kode_area),
            ]
        ];
    }

    /**
     * Format the full address by combining alamat and area info.
     */
    private function formatFullAddress(): string
    {
        $parts = [];
        if (! empty($this->alamat)) {
            $parts[] = $this->alamat;
        }
        if ($this->resource && method_exists($this, 'relationLoaded') && $this->relationLoaded('area') && $this->area) {
            $areaName = $this->area->area ?? ($this->area->nama_area ?? null);
            if (! empty($areaName)) {
                $parts[] = $areaName;
            }
        }

        return ! empty($parts) ? implode(', ', $parts) : 'Alamat tidak tersedia';
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
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'api_version' => '1.0',
            ]
        ];
    }
}
