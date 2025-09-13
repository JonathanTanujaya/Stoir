<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriResource extends JsonResource
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
            'kode_kategori' => $this->kode_kategori,
            'kategori' => $this->kategori,
            'status' => (bool) $this->status,
            'status_label' => $this->status ? 'Aktif' : 'Tidak Aktif',
            'status_badge_class' => $this->status ? 'success' : 'danger',
            
            // Relationships
            'divisi' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('divisi'), 
                function () {
                    return [
                        'kode_divisi' => $this->divisi->kode_divisi,
                        'nama_divisi' => $this->divisi->nama_divisi,
                    ];
                }
            ),
            
            // Stats
            'barangs_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('barangs'),
                function () {
                    return $this->barangs->count();
                }
            ),
            'active_barangs_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('barangs'),
                function () {
                    return $this->barangs->where('status', true)->count();
                }
            ),
            'dpakets_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('dPakets'),
                function () {
                    return $this->dPakets->count();
                }
            ),
            
            // Additional data for UI
            'display_name' => "[{$this->kode_kategori}] {$this->kategori}",
            'full_name' => "[{$this->kode_divisi}] - [{$this->kode_kategori}] {$this->kategori}",
        ];
    }
}
