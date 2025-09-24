<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnSalesDetailResource extends JsonResource
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
            'no_invoice' => $this->no_invoice,
            'kode_barang' => $this->kode_barang,
            'qty_retur' => $this->qty_retur,
            'harga_nett' => $this->harga_nett,
            'status' => $this->status,
            
            // Calculated fields
            'total_amount' => $this->qty_retur * $this->harga_nett,
            
            // Related data (loaded when available)
            'return_sales' => $this->whenLoaded('returnSales', function () {
                return [
                    'no_return_sales' => $this->returnSales->no_return_sales,
                    'tgl_return' => $this->returnSales->tgl_return,
                    'kode_cust' => $this->returnSales->kode_cust,
                    'nilai' => $this->returnSales->nilai,
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
            
            'invoice' => $this->whenLoaded('invoice', function () {
                return [
                    'no_invoice' => $this->invoice->no_invoice,
                    'tgl_faktur' => $this->invoice->tgl_faktur,
                    'kode_cust' => $this->invoice->kode_cust,
                    'total' => $this->invoice->total,
                    'status' => $this->invoice->status,
                ];
            }),
        ];
    }
}
