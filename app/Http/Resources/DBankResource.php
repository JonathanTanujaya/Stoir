<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DBankResource extends JsonResource
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
            'no_rekening' => $this->no_rekening,
            'kode_bank' => $this->kode_bank,
            'atas_nama' => $this->atas_nama,
            'saldo' => $this->saldo ? number_format($this->saldo, 2, '.', '') : '0.00',
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relasi
            'divisi' => $this->whenLoaded('divisi', function () {
                return [
                    'kode_divisi' => $this->divisi->kode_divisi,
                    'nama_divisi' => $this->divisi->nama_divisi ?? null,
                ];
            }),
            
            'bank' => $this->whenLoaded('bank', function () {
                return [
                    'kode_bank' => $this->bank->kode_bank,
                    'nama_bank' => $this->bank->nama_bank ?? null,
                ];
            }),
            
            // Statistik saldo
            'saldo_info' => [
                'formatted' => $this->saldo ? 'Rp ' . number_format($this->saldo, 2, ',', '.') : 'Rp 0,00',
                'is_positive' => $this->saldo > 0,
                'is_zero' => $this->saldo == 0,
                'status_text' => $this->getStatusText(),
            ]
        ];
    }

    /**
     * Get status text based on saldo
     */
    private function getStatusText(): string
    {
        if (!$this->saldo || $this->saldo == 0) {
            return 'Kosong';
        }
        
        if ($this->saldo > 0) {
            return 'Aktif';
        }
        
        return 'Negatif';
    }
}
