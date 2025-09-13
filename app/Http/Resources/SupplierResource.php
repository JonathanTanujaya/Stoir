<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'kode_supplier' => $this->kode_supplier,
            'nama_supplier' => $this->nama_supplier,
            'nama_divisi' => $this->whenLoaded('divisi', function () {
                return $this->divisi->nama_divisi ?? null;
            }),
            'contact_info' => [
                'alamat' => $this->alamat,
                'telp' => $this->telp,
                'contact' => $this->contact,
            ],
            'supplier_details' => [
                'status' => $this->status,
                'status_text' => $this->status ? 'Aktif' : 'Tidak Aktif',
                'full_address' => $this->formatFullAddress(),
            ],
            // 'statistics' => $this->when($this->relationLoaded('partPenerimaans'), function () {
            //     $partPenerimaans = $this->partPenerimaans;
            //     $totalPenerimaan = $partPenerimaans->count();
            //     $totalNilai = $partPenerimaans->sum('grand_total');
            //     
            //     return [
            //         'total_penerimaan' => $totalPenerimaan,
            //         'total_nilai_penerimaan' => $totalNilai,
            //         'total_nilai_formatted' => 'Rp ' . number_format($totalNilai, 0, ',', '.'),
            //         'rata_rata_nilai' => $totalPenerimaan > 0 ? $totalNilai / $totalPenerimaan : 0,
            //         'rata_rata_formatted' => $totalPenerimaan > 0 ? 
            //             'Rp ' . number_format($totalNilai / $totalPenerimaan, 0, ',', '.') : 'Rp 0',
            //         'last_penerimaan' => $partPenerimaans->sortByDesc('tgl_penerimaan')->first()?->tgl_penerimaan?->format('Y-m-d')
            //     ];
            // }),
            'metadata' => [
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            ]
        ];
    }

    /**
     * Format the full address by combining alamat and kota.
     */
    private function formatFullAddress(): string
    {
        return $this->alamat ?: 'Alamat tidak tersedia';
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
