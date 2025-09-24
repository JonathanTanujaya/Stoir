<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturPenerimaanDetailResource extends JsonResource
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
            'no_retur' => $this->no_retur,
            'no_penerimaan' => $this->no_penerimaan,
            'kode_barang' => $this->kode_barang,
            'qty_retur' => $this->qty_retur,
            'harga_nett' => $this->harga_nett,
            'status' => $this->status,
            
            // Calculated fields
            'total_amount' => $this->qty_retur * $this->harga_nett,
            
            // Related data (loaded when available)
            'retur_penerimaan' => $this->whenLoaded('returPenerimaan', function () {
                return [
                    'no_retur_penerimaan' => $this->returPenerimaan->no_retur_penerimaan,
                    'tgl_retur' => $this->returPenerimaan->tgl_retur,
                    'kode_supplier' => $this->returPenerimaan->kode_supplier,
                    'total' => $this->returPenerimaan->total,
                ];
            }),
            
            'barang' => $this->whenLoaded('barang', function () {
                return [
                    'kode_barang' => $this->barang->kode_barang,
                    'nama_barang' => $this->barang->nama_barang,
                    'satuan' => $this->barang->satuan,
                    'kode_kategori' => $this->barang->kode_kategori,
                ];
            }),
            
            'part_penerimaan' => $this->whenLoaded('partPenerimaan', function () {
                return [
                    'no_penerimaan' => $this->partPenerimaan->no_penerimaan,
                    'tgl_penerimaan' => $this->partPenerimaan->tgl_penerimaan,
                    'kode_supplier' => $this->partPenerimaan->kode_supplier,
                    'total' => $this->partPenerimaan->total,
                    'status' => $this->partPenerimaan->status,
                ];
            }),
        ];
    }
}
