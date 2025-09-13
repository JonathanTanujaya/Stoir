<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DBarangResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_divisi' => $this->kode_divisi,
            'kode_barang' => $this->kode_barang,
            'tgl_masuk' => $this->tgl_masuk?->format('Y-m-d H:i:s'),
            'modal' => $this->modal ? number_format($this->modal, 2, '.', '') : '0.00',
            'stok' => $this->stok ?? 0,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relasi
            'divisi' => $this->whenLoaded('divisi', function () {
                return [
                    'kode_divisi' => $this->divisi->kode_divisi,
                    'nama_divisi' => $this->divisi->nama_divisi ?? null,
                ];
            }),
            
            'barang' => $this->whenLoaded('barang', function () {
                return [
                    'kode_divisi' => $this->barang->kode_divisi,
                    'kode_barang' => $this->barang->kode_barang,
                    'nama_barang' => $this->barang->nama_barang ?? null,
                    'satuan' => $this->barang->satuan ?? null,
                    'kategori' => $this->barang->kategori ?? null,
                ];
            }),
            
            // Informasi stok dan nilai
            'stock_info' => [
                'is_available' => $this->stok > 0,
                'stock_status' => $this->getStockStatus(),
                'total_value' => $this->modal && $this->stok ? 
                    number_format($this->modal * $this->stok, 2, '.', '') : '0.00',
                'total_value_formatted' => $this->modal && $this->stok ? 
                    'Rp ' . number_format($this->modal * $this->stok, 2, ',', '.') : 'Rp 0,00',
                'modal_formatted' => $this->modal ? 
                    'Rp ' . number_format($this->modal, 2, ',', '.') : 'Rp 0,00',
            ],
            
            // Statistik pergerakan stok (jika dimuat)
            'stock_movements_count' => $this->whenCounted('stockMovements'),
        ];
    }

    /**
     * Get stock status based on stock quantity
     */
    private function getStockStatus(): string
    {
        if (!$this->stok || $this->stok == 0) {
            return 'Habis';
        }
        
        if ($this->stok < 5) {
            return 'Rendah';
        }
        
        if ($this->stok < 20) {
            return 'Sedang';
        }
        
        return 'Tersedia';
    }
}
