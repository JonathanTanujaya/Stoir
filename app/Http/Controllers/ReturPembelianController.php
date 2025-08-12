<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReturPembelianController extends Controller
{
    public function index()
    {
        try {
            $returns = DB::table('dbo.returpenerimaan')
                ->select([
                    'kodedivisi',
                    'noretur',
                    'tglretur',
                    'kodesupplier',
                    'total',
                    'sisaretur',
                    'keterangan',
                    'status'
                ])
                ->orderBy('tglretur', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($return) {
                    return [
                        'id' => $return->kodedivisi . '-' . $return->noretur,
                        'return_number' => $return->noretur,
                        'return_date' => $return->tglretur,
                        'supplier_code' => $return->kodesupplier,
                        'total' => (float)($return->total ?? 0),
                        'remaining' => (float)($return->sisaretur ?? 0),
                        'notes' => $return->keterangan,
                        'status' => $return->status
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $returns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve returns: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'header.returnDate' => 'required|date',
            'header.supplier_id' => 'required|string',
            'header.originalPurchase' => 'required|string',
            'header.notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.code' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.reason' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $header = $request->input('header');
            $items = $request->input('items');

            // Calculate total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['qty'] * $item['price'];
            }

            // Get supplier info
            $supplier = DB::table('dbo.m_supplier')
                ->where('kodesupplier', $header['supplier_id'])
                ->first();

            if (!$supplier) {
                throw new \Exception('Supplier not found');
            }

            // Generate return number
            $lastNo = DB::table('dbo.returpenerimaan')
                ->where('kodedivisi', '01')
                ->where('noretur', 'like', '%/RO/%')
                ->max('noretur');
            
            // Extract number from format like "2021/11/RO/0001"
            $currentYear = date('Y');
            $currentMonth = date('m');
            $newNo = $currentYear . '/' . $currentMonth . '/RO/' . str_pad(1, 4, '0', STR_PAD_LEFT);
            
            if ($lastNo) {
                preg_match('/(\d{4})\/(\d{2})\/RO\/(\d{4})/', $lastNo, $matches);
                if (!empty($matches) && $matches[1] == $currentYear && $matches[2] == $currentMonth) {
                    $newNo = $currentYear . '/' . $currentMonth . '/RO/' . str_pad(((int)$matches[3]) + 1, 4, '0', STR_PAD_LEFT);
                }
            }

            // Create return header
            DB::table('dbo.returpenerimaan')->insert([
                'kodedivisi' => '01',
                'noretur' => $newNo,
                'tglretur' => $header['returnDate'],
                'kodesupplier' => $header['supplier_id'],
                'total' => $total,
                'sisaretur' => $total,
                'keterangan' => $header['notes'] ?? '',
                'status' => 'Open'
            ]);

            // Create return details
            foreach ($items as $index => $item) {
                DB::table('dbo.returpenerimaan_detail')->insert([
                    'kodedivisi' => '01',
                    'noretur' => $newNo,
                    'kodebarang' => $item['code'],
                    'qtyretur' => $item['qty'],
                    'harganett' => $item['price'],
                    'alasan' => $item['reason'],
                    'status' => 'Open'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Return created successfully',
                'data' => [
                    'id' => '01-' . $newNo,
                    'return_number' => $newNo,
                    'total' => $total
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create return: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPurchases()
    {
        try {
            $purchases = DB::table('dbo.partpenerimaan')
                ->select([
                    'kodedivisi',
                    'nopenerimaan',
                    'nofaktur',
                    'tglpenerimaan',
                    'kodesupplier'
                ])
                ->where('status', 'Open')
                ->whereNotNull('nofaktur')
                ->orderBy('tglpenerimaan', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($purchase) {
                    return [
                        'id' => $purchase->kodedivisi . '-' . $purchase->nopenerimaan,
                        'purchase_number' => $purchase->nopenerimaan,
                        'invoice_number' => $purchase->nofaktur,
                        'purchase_date' => $purchase->tglpenerimaan,
                        'supplier_code' => $purchase->kodesupplier,
                        'display_text' => $purchase->nofaktur . ' - ' . $purchase->nopenerimaan . ' (' . $purchase->tglpenerimaan . ')'
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $purchases
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve purchases: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPurchaseDetails($purchaseId)
    {
        try {
            $details = DB::table('dbo.partpenerimaan_detail')
                ->join('dbo.partpenerimaan', function($join) {
                    $join->on('dbo.partpenerimaan_detail.kodedivisi', '=', 'dbo.partpenerimaan.kodedivisi')
                         ->on('dbo.partpenerimaan_detail.nopenerimaan', '=', 'dbo.partpenerimaan.nopenerimaan');
                })
                ->leftJoin('dbo.barang', 'dbo.partpenerimaan_detail.kodebarang', '=', 'dbo.barang.kodebarang')
                ->where('dbo.partpenerimaan_detail.nopenerimaan', $purchaseId)
                ->select([
                    'dbo.partpenerimaan_detail.kodebarang',
                    'dbo.barang.namabarang',
                    'dbo.partpenerimaan_detail.qtysupply',
                    'dbo.partpenerimaan_detail.harganett'
                ])
                ->get()
                ->map(function ($detail) {
                    return [
                        'code' => $detail->kodebarang,
                        'name' => $detail->namabarang ?? 'Unknown Product',
                        'qty_purchased' => (float)($detail->qtysupply ?? 0),
                        'price' => (float)($detail->harganett ?? 0)
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve purchase details: ' . $e->getMessage()
            ], 500);
        }
    }
}
