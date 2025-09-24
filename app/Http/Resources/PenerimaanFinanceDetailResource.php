<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenerimaanFinanceDetailResource extends JsonResource
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
            'no_penerimaan' => $this->no_penerimaan,
            'no_invoice' => $this->no_invoice,
            'jumlah_invoice' => $this->jumlah_invoice,
            'sisa_invoice' => $this->sisa_invoice,
            'jumlah_bayar' => $this->jumlah_bayar,
            'jumlah_dispensasi' => $this->jumlah_dispensasi,
            'status' => $this->status,
            
            // Calculated fields
            'total_pembayaran' => $this->jumlah_bayar + $this->jumlah_dispensasi,
            'sisa_tagihan' => $this->jumlah_invoice - ($this->jumlah_bayar + $this->jumlah_dispensasi),
            'persentase_pembayaran' => $this->jumlah_invoice > 0 
                ? round((($this->jumlah_bayar + $this->jumlah_dispensasi) / $this->jumlah_invoice) * 100, 2)
                : 0,
            
            // Relationships
            'penerimaan_finance' => $this->whenLoaded('penerimaanFinance', function () {
                return [
                    'no_penerimaan' => $this->penerimaanFinance->no_penerimaan ?? null,
                    'tanggal' => $this->penerimaanFinance->tanggal ?? null,
                ];
            }),
            'invoice' => $this->whenLoaded('invoice', function () {
                return [
                    'no_invoice' => $this->invoice->no_invoice ?? null,
                    'tgl_faktur' => $this->invoice->tgl_faktur ?? null,
                    'grand_total' => $this->invoice->grand_total ?? null,
                    'kode_cust' => $this->invoice->kode_cust ?? null,
                ];
            }),
        ];
    }
}
