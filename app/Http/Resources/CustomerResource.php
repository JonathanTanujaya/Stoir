<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'kode_divisi' => $this->kode_divisi,
            'kode_cust' => $this->kode_cust,
            'nama_cust' => $this->nama_cust,
            
            // Contact Information
            'contact_info' => [
                'alamat' => $this->alamat,
                'telp' => $this->telp,
                'contact' => $this->contact,
            ],
            
            // Business Information
            'business_info' => [
                'credit_limit' => [
                    'amount' => $this->credit_limit,
                    'formatted' => $this->credit_limit ? 'Rp ' . number_format($this->credit_limit, 0, ',', '.') : 'Rp 0'
                ],
                'jatuh_tempo' => $this->jatuh_tempo,
                'jatuh_tempo_text' => $this->jatuh_tempo ? $this->jatuh_tempo . ' hari' : 'Tidak ditentukan',
                'status' => $this->status,
                'status_text' => $this->status ? 'Aktif' : 'Tidak Aktif'
            ],
            
            // Tax Information
            'tax_info' => [
                'no_npwp' => $this->no_npwp,
                'nama_pajak' => $this->nama_pajak,
                'alamat_pajak' => $this->alamat_pajak,
            ],
            
            // Metadata
            'meta' => [
                'created_at' => $this->created_at?->toISOString(),
                'created_at_formatted' => $this->created_at?->format('d/m/Y H:i:s'),
            ],
            
            // Relationships
            'relationships' => [
                'divisi' => $this->whenLoaded('divisi', function () {
                    return [
                        'kode_divisi' => $this->divisi->kode_divisi,
                        'nama_divisi' => $this->divisi->nama_divisi
                    ];
                }),
                'area' => $this->whenLoaded('area', function () {
                    return [
                        'kode_area' => $this->area->kode_area,
                        'nama_area' => $this->area->nama_area
                    ];
                }),
                'sales' => $this->whenLoaded('sales', function () {
                    return [
                        'kode_sales' => $this->sales->kode_sales,
                        'nama_sales' => $this->sales->nama_sales
                    ];
                })
            ],
            
            // Statistics (when counts are loaded)
            'statistics' => [
                'total_invoices' => $this->whenCounted('invoices'),
                'total_returns' => $this->whenCounted('returnSales'),
                'total_tts' => $this->whenCounted('mTts'),
                'total_resis' => $this->whenCounted('mResis'),
                'total_penerimaan_finances' => $this->whenCounted('penerimaanFinances')
            ]
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'api_version' => '1.0',
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Customize the response for a request.
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'Customer');
        $response->header('X-API-Version', '1.0');
    }
}
