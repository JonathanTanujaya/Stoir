<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource
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
            'kode_barang' => $this->kode_barang,
            'nama_barang' => $this->nama_barang,
            'kode_kategori' => $this->kode_kategori,
            'nama_kategori' => $this->whenLoaded('kategori', function () {
                return $this->kategori->nama_kategori ?? null;
            }),
            'nama_divisi' => $this->whenLoaded('divisi', function () {
                return $this->divisi->nama_divisi ?? null;
            }),
            'pricing' => [
                'harga_list' => $this->formatCurrency($this->harga_list),
                'harga_jual' => $this->formatCurrency($this->harga_jual),
                'harga_list_raw' => (float) $this->harga_list,
                'harga_jual_raw' => (float) $this->harga_jual,
            ],
            'inventory' => [
                'satuan' => $this->satuan,
                'stok_min' => (int) $this->stok_min,
                'lokasi' => $this->lokasi,
                'status' => $this->status,
                'status_text' => $this->status ? 'Active' : 'Inactive',
            ],
            'discounts' => [
                'disc1' => (float) $this->disc1,
                'disc2' => (float) $this->disc2,
                'disc1_formatted' => $this->disc1 . '%',
                'disc2_formatted' => $this->disc2 . '%',
            ],
            'product_info' => [
                'merk' => $this->merk,
                'barcode' => $this->barcode,
            ],
            'meta' => [
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
                'created_at_human' => $this->created_at?->diffForHumans(),
                'updated_at_human' => $this->updated_at?->diffForHumans(),
            ],
            // Include relationships when loaded
            'relationships' => [
                'divisi' => $this->whenLoaded('divisi'),
                'kategori' => $this->whenLoaded('kategori'),
                'detail_barang' => $this->whenLoaded('detailBarang'),
                'stock_movements_count' => $this->whenCounted('kartuStoks'),
                'invoice_details_count' => $this->whenCounted('invoiceDetails'),
            ]
        ];
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
     * Get additional data to be added to the response.
     */
    public function with(Request $request): array
    {
        return [
            'api_version' => '1.0',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, $response): void
    {
        $response->header('X-Resource-Type', 'BarangResource');
        $response->header('X-API-Version', '1.0');
    }
}
