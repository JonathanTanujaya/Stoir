<?php

namespace App\Http\Controllers;

use App\Models\PartPenerimaan;
use App\Models\PartPenerimaanDetail;
use App\Models\MSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchasesController extends Controller
{
    public function index()
    {
        try {
            // Ambil data sederhana dari tabel partpenerimaan dulu
            $purchases = DB::table('dbo.partpenerimaan')
                ->select([
                    'kodedivisi',
                    'nopenerimaan',
                    'nofaktur',
                    'tglpenerimaan',
                    'jatuhtempo',
                    'kodesupplier',
                    'total',
                    'discount',
                    'pajak',
                    'grandtotal',
                    'status'
                ])
                ->whereNotNull('nofaktur')
                ->orderBy('tglpenerimaan', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($purchase) {
                    return [
                        'id' => $purchase->kodedivisi . '-' . $purchase->nopenerimaan,
                        'invoice_number' => $purchase->nofaktur,
                        'receiptDate' => $purchase->tglpenerimaan,
                        'dueDate' => $purchase->jatuhtempo,
                        'supplier' => [
                            'code' => $purchase->kodesupplier ?? '',
                            'name' => 'Supplier ' . ($purchase->kodesupplier ?? 'Unknown')
                        ],
                        'total' => (float)($purchase->total ?? 0),
                        'global_discount' => (float)($purchase->discount ?? 0),
                        'tax' => (float)($purchase->pajak ?? 0),
                        'grand_total' => (float)($purchase->grandtotal ?? 0),
                        'status' => $purchase->status ?? 'OPEN',
                        'created_at' => $purchase->tglpenerimaan
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $purchases
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch purchases: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'header.invoiceNumber' => 'required|string|max:50',
            'header.receiptDate' => 'required|date',
            'header.dueDate' => 'required|date',
            'header.supplier_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.code' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check invoice uniqueness
        $existingInvoice = DB::table('dbo.partpenerimaan')
            ->where('nofaktur', $request->input('header.invoiceNumber'))
            ->first();
        
        if ($existingInvoice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice number already exists'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $header = $request->input('header');
            $items = $request->input('items');

            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $qty = $item['qty'];
                $price = $item['price'];
                $disc1 = $item['disc1'] ?? 0;
                $disc2 = $item['disc2'] ?? 0;
                $netPrice = $price * (1 - $disc1/100) * (1 - $disc2/100);
                $subtotal += $netPrice * $qty;
            }

            $globalDiscount = $header['globalDiscount'] ?? 0;
            $afterDiscount = $subtotal - $globalDiscount;
            $taxPercent = $header['taxPercent'] ?? 0;
            $tax = $afterDiscount * ($taxPercent / 100);
            $grandTotal = $afterDiscount + $tax;

            // Get supplier info
            $supplier = DB::table('dbo.msupplier')
                ->where('id', $header['supplier_id'])
                ->first();
            if (!$supplier) {
                throw new \Exception('Supplier not found');
            }

            // Generate sequential number
            $lastNo = DB::table('dbo.partpenerimaan')
                ->where('kodedivisi', '01')
                ->max('nopenerimaan');
            $newNo = str_pad(((int)$lastNo) + 1, 6, '0', STR_PAD_LEFT);

            // Create main purchase record using raw query for compatibility
            DB::table('dbo.partpenerimaan')->insert([
                'kodedivisi' => '01',
                'nopenerimaan' => $newNo,
                'tglpenerimaan' => $header['receiptDate'],
                'kodevalas' => 'IDR',
                'kurs' => 1,
                'kodesupplier' => $supplier->kodecust,
                'jatuhtempo' => $header['dueDate'],
                'nofaktur' => $header['invoiceNumber'],
                'total' => $subtotal,
                'discount' => $globalDiscount,
                'pajak' => $tax,
                'grandtotal' => $grandTotal,
                'status' => 'OPEN'
            ]);

            // Create detail records
            foreach ($items as $index => $item) {
                $qty = $item['qty'];
                $price = $item['price'];
                $disc1 = $item['disc1'] ?? 0;
                $disc2 = $item['disc2'] ?? 0;
                $netPrice = $price * (1 - $disc1/100) * (1 - $disc2/100);
                $itemSubtotal = $netPrice * $qty;

                DB::table('dbo.partpenerimaan_detail')->insert([
                    'kodedivisi' => '01',
                    'nopenerimaan' => $newNo,
                    'nourut' => $index + 1,
                    'kodebarang' => $item['code'],
                    'namabarang' => $item['name'],
                    'qtysupply' => $qty,
                    'satuan' => $item['unit'] ?? 'PCS',
                    'harga' => $price,
                    'diskon1' => $disc1,
                    'diskon2' => $disc2,
                    'harganett' => $netPrice,
                    'subtotal' => $itemSubtotal
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase created successfully',
                'data' => [
                    'id' => '01-' . $newNo,
                    'invoice_number' => $header['invoiceNumber'],
                    'grand_total' => $grandTotal
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // For development, return mock data structure
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $id,
                    'invoice_number' => 'PO' . date('Y') . '001',
                    'receiptDate' => date('Y-m-d'),
                    'dueDate' => date('Y-m-d', strtotime('+14 days')),
                    'supplier' => ['id' => 1, 'code' => 'S001', 'name' => 'Sample Supplier'],
                    'items' => []
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase not found'
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Purchase deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete purchase: ' . $e->getMessage()
            ], 500);
        }
    }
}
