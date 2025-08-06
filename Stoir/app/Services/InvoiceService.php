<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\MCust;
use App\Models\DBarang;
use App\Models\KartuStok;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function createInvoice(
        $KodeDivisi,
        $NoInvoice,
        $KodeCust,
        $KodeSales,
        $tipe,
        $total,
        $disc,
        $pajak,
        $GrandTotal,
        $ket,
        $KodeBarang,
        $QtySupply,
        $hargajual,
        $diskon1,
        $diskon2,
        $harganett,
        $user
    )
    {
        DB::transaction(function () use (
            $KodeDivisi,
            $NoInvoice,
            $KodeCust,
            $KodeSales,
            $tipe,
            $total,
            $disc,
            $pajak,
            $GrandTotal,
            $ket,
            $KodeBarang,
            $QtySupply,
            $hargajual,
            $diskon1,
            $diskon2,
            $harganett,
            $user
        ) {
            // Logic from SP_Invoice
            $invoiceExists = Invoice::where('KodeDivisi', $KodeDivisi)
                ->where('NoInvoice', $NoInvoice)
                ->exists();

            if (!$invoiceExists) {
                $customer = MCust::where('KodeDivisi', $KodeDivisi)
                    ->where('KodeCust', $KodeCust)
                    ->first();

                $jatuhTempo = null;
                if ($customer && $customer->JatuhTempo) {
                    $jatuhTempo = now()->addDays($customer->JatuhTempo);
                }

                Invoice::create([
                    'KodeDivisi' => $KodeDivisi,
                    'NoInvoice' => $NoInvoice,
                    'TglFaktur' => now(),
                    'KodeCust' => $KodeCust,
                    'KodeSales' => $KodeSales,
                    'Tipe' => $tipe,
                    'JatuhTempo' => $jatuhTempo,
                    'Total' => $total,
                    'Disc' => $disc,
                    'Pajak' => $pajak,
                    'GrandTotal' => $GrandTotal,
                    'SisaInvoice' => $GrandTotal,
                    'Ket' => $ket,
                    'Status' => 'Open',
                    'username' => $user,
                ]);
            }

            InvoiceDetail::create([
                'KodeDivisi' => $KodeDivisi,
                'NoInvoice' => $NoInvoice,
                'KodeBarang' => $KodeBarang,
                'QtySupply' => $QtySupply,
                'HargaJual' => $hargajual,
                'Diskon1' => $diskon1,
                'Diskon2' => $diskon2,
                'HargaNett' => $harganett,
                'Status' => 'Open',
            ]);

            // Stock update logic from SP_Invoice
            $remainingQtySupply = $QtySupply;
            $dBarangItems = DBarang::where('KodeDivisi', $KodeDivisi)
                ->where('KodeBarang', $KodeBarang)
                ->where('Stok', '>', 0)
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
                    'Tipe' => 'Penjualan',
                    'Increase' => 0,
                    'Decrease' => $qtyToProcess,
                    'Harga_Debet' => 0,
                    'Harga_Kredit' => $harganett - ($harganett * $disc / 100),
                    'Qty' => $qtyBaru,
                    'HPP' => $item->Modal,
                ]);

                $remainingQtySupply -= $qtyToProcess;
            }
        });
    }
}
