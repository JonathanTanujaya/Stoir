<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate derived values
        $grossAmount = $this->qty_supply * $this->harga_jual;
        $totalDiscount = ($this->diskon1 ?? 0) + ($this->diskon2 ?? 0);
        
        return [
            'id' => $this->id,
            'kode_divisi' => $this->kode_divisi,
            'no_invoice' => $this->no_invoice,
            'kode_barang' => $this->kode_barang,
            'qty_supply' => $this->qty_supply,
            'harga_jual' => $this->harga_jual,
            'jenis' => $this->jenis,
            'diskon1' => $this->diskon1,
            'diskon2' => $this->diskon2,
            'harga_nett' => $this->harga_nett,
            'status' => $this->status,
            
            // Calculated fields
            'gross_amount' => $grossAmount,
            'total_discount_percent' => $totalDiscount,
            'discount_amount' => $grossAmount - $this->harga_nett,
            'unit_nett_price' => $this->qty_supply > 0 ? $this->harga_nett / $this->qty_supply : 0,
            
            // Display formatting
            'display_name' => $this->kode_barang . ' (Qty: ' . $this->qty_supply . ')',
            'formatted_gross_amount' => 'Rp ' . number_format($grossAmount, 2, ',', '.'),
            'formatted_nett_amount' => 'Rp ' . number_format($this->harga_nett, 2, ',', '.'),
            
            // Status styling
            'status_badge_class' => $this->getStatusBadgeClass(),
            
            // Relationship data
            'invoice' => $this->whenLoaded('invoice', function () {
                return [
                    'kode_divisi' => $this->invoice->kode_divisi,
                    'no_invoice' => $this->invoice->no_invoice,
                    'tgl_faktur' => $this->invoice->tgl_faktur?->toDateString(),
                    'customer_name' => $this->invoice->customer?->nama_cust ?? null,
                ];
            }),
            
            'barang' => $this->whenLoaded('barang', function () {
                return [
                    'kode_divisi' => $this->barang->kode_divisi,
                    'kode_barang' => $this->barang->kode_barang,
                    'nama_barang' => $this->barang->nama_barang,
                    'satuan' => $this->barang->satuan ?? null,
                    'status' => $this->barang->status,
                ];
            }),
            
            'divisi' => $this->whenLoaded('divisi', function () {
                return [
                    'kode_divisi' => $this->divisi->kode_divisi,
                    'nama_divisi' => $this->divisi->nama_divisi,
                ];
            }),
            
            // Metadata
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get status badge CSS class
     */
    private function getStatusBadgeClass(): string
    {
        return match(strtolower($this->status ?? '')) {
            'active', 'aktif' => 'badge-success',
            'inactive', 'tidak aktif' => 'badge-secondary',
            'pending' => 'badge-warning',
            'cancelled', 'dibatalkan' => 'badge-danger',
            default => 'badge-primary'
        };
    }
}
