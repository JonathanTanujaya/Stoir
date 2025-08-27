<?php

namespace App\Services;

use App\Models\MCustDiskonDetail;

class ProductService
{
    public function getDiskon(
        $kodedivisi,
        $kodecust,
        $kodebarang,
        $qtyPengambilan
    )
    {
        // Logic from SP_GetDiskon
        $diskon = MCustDiskonDetail::where('KodeDivisi', $kodedivisi)
            ->where('KodeCust', $kodecust)
            ->where('KodeBarang', $kodebarang)
            ->where('Qtymin', '<=', $qtyPengambilan)
            ->where('QtyMax', '>=', $qtyPengambilan)
            ->value('diskon');

        return $diskon ?? 0;
    }

    public function getDiskonDetail(
        $kodedivisi,
        $kodecust,
        $kodebarang,
        $qtyPengambilan
    )
    {
        // Logic from F_GetDiskon
        $diskonDetail = MCustDiskonDetail::where('KodeDivisi', $kodedivisi)
            ->where('KodeCust', $kodecust)
            ->where('KodeBarang', $kodebarang)
            ->where('Qtymin', '<=', $qtyPengambilan)
            ->where('QtyMax', '>=', $qtyPengambilan)
            ->select('Diskon1', 'Diskon2', 'jenis')
            ->first();

        return $diskonDetail;
    }

    public function getStokClaim($kodedivisi, $kodebarang)
    {
        // Logic from F_GetStokClaim
        $stokClaim = \App\Models\StokClaim::where('KodeBarang', $kodebarang)->sum('StokClaim');

        return $stokClaim ?? 0;
    }

    public function getStokClaimQuery()
    {
        return 'SELECT COALESCE(SUM(StokClaim), 0) FROM stok_claim WHERE KodeBarang = barang.KodeBarang';
    }
}
