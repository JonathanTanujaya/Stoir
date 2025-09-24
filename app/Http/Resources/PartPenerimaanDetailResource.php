<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartPenerimaanDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate derived values
        $grossAmount = $this->qty_supply * $this->harga;
        $totalDiscount = ($this->diskon1 ?? 0) + ($this->diskon2 ?? 0);
        $discountAmount = $grossAmount - $this->harga_nett;
        
        return [
            'no_penerimaan' => $this->no_penerimaan,
            'kode_barang' => $this->kode_barang,
            'qty_supply' => $this->qty_supply,
            'harga' => $this->harga,
            'diskon1' => $this->diskon1,
            'diskon2' => $this->diskon2,
            'harga_nett' => $this->harga_nett,
            
            // Calculated fields
            'gross_amount' => $grossAmount,
            'total_discount_percent' => $totalDiscount,
            'discount_amount' => $discountAmount,
            'unit_nett_price' => $this->qty_supply > 0 ? $this->harga_nett / $this->qty_supply : 0,
            'effective_discount_percent' => $grossAmount > 0 ? ($discountAmount / $grossAmount) * 100 : 0,
            
            // Display formatting
            'display_name' => $this->kode_barang . ' (Qty: ' . $this->qty_supply . ')',
            'formatted_gross_amount' => 'Rp ' . number_format($grossAmount, 2, ',', '.'),
            'formatted_nett_amount' => 'Rp ' . number_format($this->harga_nett, 2, ',', '.'),
            'formatted_unit_price' => 'Rp ' . number_format($this->harga, 2, ',', '.'),
            
            // Composite identifier for single-tenant structure
            'composite_key' => $this->no_penerimaan . '-' . $this->kode_barang,
            
            // Relationship data
            'part_penerimaan' => $this->whenLoaded('partPenerimaan', function () {
                return [
                    'no_penerimaan' => $this->partPenerimaan->no_penerimaan,
                    'tanggal' => $this->partPenerimaan->tanggal,
                    'supplier_name' => $this->partPenerimaan->supplier_name ?? 'N/A',
                ];
            }),
            
            'barang' => $this->whenLoaded('barang', function () {
                return [
                    'kode_barang' => $this->barang->kode_barang,
                    'barang' => $this->barang->barang,
                    'satuan' => $this->barang->satuan ?? 'N/A',
                    'status' => $this->barang->status,
                ];
            }),
            
            // Note about primary key limitation
            'note' => 'Individual updates/deletes not supported due to lack of primary key in database schema',
        ];
    }
}
