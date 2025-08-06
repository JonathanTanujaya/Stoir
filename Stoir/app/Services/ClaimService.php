<?php

namespace App\Services;

use App\Models\ClaimPenjualan;
use App\Models\ClaimPenjualanDetail;
use App\Models\DBarang;
use App\Models\StokClaim;
use Illuminate\Support\Facades\DB;

class ClaimService
{
    public function createClaim(
        $kodedivisi,
        $noclaim,
        $kodecust,
        $ket,
        $noinvoice,
        $kodebarang,
        $qtyclaim
    )
    {
        DB::transaction(function () use (
            $kodedivisi,
            $noclaim,
            $kodecust,
            $ket,
            $noinvoice,
            $kodebarang,
            $qtyclaim
        ) {
            // Logic from SP_Claim
            $claimExists = ClaimPenjualan::where('KodeDivisi', $kodedivisi)
                ->where('NoClaim', $noclaim)
                ->exists();

            if (!$claimExists) {
                ClaimPenjualan::create([
                    'KodeDivisi' => $kodedivisi,
                    'NoClaim' => $noclaim,
                    'TglClaim' => now(),
                    'KodeCust' => $kodecust,
                    'Keterangan' => $ket,
                    'Status' => 'Finish',
                ]);
            }

            ClaimPenjualanDetail::create([
                'KodeDivisi' => $kodedivisi,
                'NoClaim' => $noclaim,
                'NoInvoice' => $noinvoice,
                'KodeBarang' => $kodebarang,
                'QtyClaim' => $qtyclaim,
                'Status' => 'Open',
            ]);

            // Stock update logic from SP_Claim
            $remainingQtyClaim = $qtyclaim;
            $dBarangItems = DBarang::where('KodeDivisi', $kodedivisi)
                ->where('KodeBarang', $kodebarang)
                ->orderBy('TglMasuk', 'asc')
                ->get();

            foreach ($dBarangItems as $item) {
                if ($remainingQtyClaim <= 0) {
                    break;
                }

                $stok = $item->Stok;
                $modal = $item->Modal;

                if ($remainingQtyClaim >= $stok) {
                    if (!StokClaim::where('KodeBarang', $kodebarang)->where('Modal', $modal)->exists()) {
                        StokClaim::create([
                            'KodeBarang' => $kodebarang,
                            'Modal' => $modal,
                            'StokClaim' => $stok,
                        ]);
                    } else {
                        StokClaim::where('KodeBarang', $kodebarang)
                            ->where('Modal', $modal)
                            ->increment('StokClaim', $stok);
                    }

                    $item->Stok = 0;
                    $item->save();

                    $remainingQtyClaim -= $stok;
                } else {
                    if (!StokClaim::where('KodeBarang', $kodebarang)->where('Modal', $modal)->exists()) {
                        StokClaim::create([
                            'KodeBarang' => $kodebarang,
                            'Modal' => $modal,
                            'StokClaim' => $remainingQtyClaim,
                        ]);
                    } else {
                        StokClaim::where('KodeBarang', $kodebarang)
                            ->where('Modal', $modal)
                            ->increment('StokClaim', $remainingQtyClaim);
                    }

                    $item->Stok -= $remainingQtyClaim;
                    $item->save();

                    $remainingQtyClaim = 0;
                }
            }
        });
    }
}
