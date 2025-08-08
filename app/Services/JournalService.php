<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Invoice;
use App\Models\ReturnSales;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function createJournalInvoice($noinvoice)
    {
        DB::transaction(function () use ($noinvoice) {
            $invoice = Invoice::where('NoInvoice', $noinvoice)->first();

            if (!$invoice) {
                throw new \Exception('Invoice not found.');
            }

            $grandTotal = $invoice->GrandTotal;
            $total = $invoice->Total;


            $insentif = 1; // Assuming 1% as per SQL script

            // Journal entries based on SP_JournalInvoice
            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Penjualan',
                'KodeCOA' => '1-1210',
                'Keterangan' => $noinvoice,
                'Debet' => $grandTotal,
                'Credit' => 0,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Penjualan',
                'KodeCOA' => '4-1001',
                'Keterangan' => $noinvoice,
                'Debet' => 0,
                'Credit' => $total,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Penjualan',
                'KodeCOA' => '2-1301',
                'Keterangan' => $noinvoice,
                'Debet' => 0,
                'Credit' => $grandTotal - $total,
            ]);

            // HPP & Persediaan
            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Penjualan',
                'KodeCOA' => '5-1001',
                'Keterangan' => $noinvoice,
                'Debet' => $hpp,
                'Credit' => 0,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Penjualan',
                'KodeCOA' => '1-1301',
                'Keterangan' => $noinvoice,
                'Debet' => 0,
                'Credit' => $hpp,
            ]);

            // Beban Insentif Pemasaran
            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Penjualan',
                'KodeCOA' => '6-1020',
                'Keterangan' => $noinvoice,
                'Debet' => ($insentif * $total) / 100,
                'Credit' => 0,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Penjualan',
                'KodeCOA' => '2-1470',
                'Keterangan' => $noinvoice,
                'Debet' => 0,
                'Credit' => ($insentif * $total) / 100,
            ]);
        });
    }

    public function createJournalReturSales($noretur)
    {
        DB::transaction(function () use ($noretur) {
            $returnSales = ReturnSales::where('NoRetur', $noretur)->first();

            if (!$returnSales) {
                throw new \Exception('Return Sales not found.');
            }

            $totalRetur = $returnSales->Total;


            $insentif = 1; // Assuming 1% as per SQL script

            // Journal entries based on SP_JournalReturSales
            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Retur Penjualan',
                'KodeCOA' => '4-1001',
                'Keterangan' => $noretur,
                'Debet' => $totalRetur / 1.1,
                'Credit' => 0,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Retur Penjualan',
                'KodeCOA' => '2-1301',
                'Keterangan' => $noretur,
                'Debet' => $totalRetur - ($totalRetur / 1.1),
                'Credit' => 0,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Retur Penjualan',
                'KodeCOA' => '1-1210',
                'Keterangan' => $noretur,
                'Debet' => 0,
                'Credit' => $totalRetur,
            ]);

            // HPP & Persediaan
            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Retur Penjualan',
                'KodeCOA' => '1-1301',
                'Keterangan' => $noretur,
                'Debet' => $hpp,
                'Credit' => 0,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Retur Penjualan',
                'KodeCOA' => '5-1001',
                'Keterangan' => $noretur,
                'Debet' => 0,
                'Credit' => $hpp,
            ]);

            // Beban Insentif Pemasaran
            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Retur Penjualan',
                'KodeCOA' => '6-1020',
                'Keterangan' => $noretur,
                'Debet' => ($insentif * $totalRetur) / 100,
                'Credit' => 0,
            ]);

            Journal::create([
                'tanggal' => now(),
                'Transaksi' => 'Retur Penjualan',
                'KodeCOA' => '2-1470',
                'Keterangan' => $noretur,
                'Debet' => 0,
                'Credit' => ($insentif * $totalRetur) / 100,
            ]);
        });
    }
}
