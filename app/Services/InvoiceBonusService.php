<?php

namespace App\Services;

use App\Models\InvoiceBonus;
use App\Models\InvoiceBonusDetail;
use App\Models\DBarang;
use App\Models\KartuStok;
use Illuminate\Support\Facades\DB;

class InvoiceBonusService
{
    public function createInvoiceBonus(
        $KodeDivisi,
        $NoInvoice,
        $KodeCust,
        $KodeBarang,
        $QtySupply,
        $user
    )
    {
        DB::transaction(function () use (
            $KodeDivisi,
            $NoInvoice,
            $KodeCust,
            $KodeBarang,
            $QtySupply,
            $user
        ) {
            // Logic from SP_InvoiceBonus
            $invoiceBonusExists = InvoiceBonus::where('KodeDivisi', $KodeDivisi)
                ->where('NoInvoiceBonus', $NoInvoice)
                ->exists();

            if (!$invoiceBonusExists) {
                InvoiceBonus::create([
                    'KodeDivisi' => $KodeDivisi,
                    'NoInvoiceBonus' => $NoInvoice,
                    'TglFaktur' => now(),
                    'KodeCust' => $KodeCust,
                    'Ket' => 'Bonus',
                    'Status' => 'Open',
                    'username' => $user,
                ]);
            }

            InvoiceBonusDetail::create([
                'KodeDivisi' => $KodeDivisi,
                'NoInvoiceBonus' => $NoInvoice,
                'KodeBarang' => $KodeBarang,
                'QtySupply' => $QtySupply,
                'HargaNett' => 0,
                'Status' => 'Open',
            ]);

            // Stock update logic from SP_InvoiceBonus
            $remainingQtySupply = $QtySupply;
            $dBarangItems = DBarang::where('KodeDivisi', $KodeDivisi)
                ->where('KodeBarang', $KodeBarang)
                ->where('Stok', '>', 0)
                ->orderBy('Modal', 'asc')
                ->orderBy('TglMasuk', 'asc')
                ->get();

            foreach ($dBarangItems as $item) {
                if ($remainingQtySupply <= 0) {
                    break;
                }

                $qtyToProcess = min($remainingQtySupply, $item->Stok);

                $item->Stok -= $qtyToProcess;
                $item->save();

                $qtyBaru = DBarang::where('KodeBarang', $KodeBarang)->sum('Stok');

                KartuStok::create([
                    'KodeDivisi' => $KodeDivisi,
                    'KodeBarang' => $KodeBarang,
                    'No_Ref' => $NoInvoice,
                    'TglProses' => now(),
                    'Tipe' => 'PenjualanBonus',
                    'Increase' => 0,
                    'Decrease' => $qtyToProcess,
                    'Harga_Debet' => 0,
                    'Harga_Kredit' => 0,
                    'Qty' => $qtyBaru,
                    'HPP' => $item->Modal,
                ]);

                $remainingQtySupply -= $qtyToProcess;
            }
        });
    }
}
