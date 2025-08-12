<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReturPenjualanController extends Controller
{
    public function index()
    {
        try {
            $returns = DB::table('dbo.returnsales')
                ->select([
                    'kodedivisi',
                    'noretur',
                    'tglretur',
                    'kodecust',
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
                        'customer_code' => $return->kodecust,
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
            'header.customer_id' => 'required|string',
            'header.originalInvoice' => 'required|string',
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

            // Get customer info
            $customer = DB::table('dbo.m_cust')
                ->where('kodecust', $header['customer_id'])
                ->first();

            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            // Generate return number
            $lastNo = DB::table('dbo.returnsales')
                ->where('kodedivisi', '01')
                ->where('noretur', 'like', '%/RS/%')
                ->max('noretur');
            
            // Extract number from format like "2021/11/RS/0001"
            $currentYear = date('Y');
            $currentMonth = date('m');
            $newNo = $currentYear . '/' . $currentMonth . '/RS/' . str_pad(1, 4, '0', STR_PAD_LEFT);
            
            if ($lastNo) {
                preg_match('/(\d{4})\/(\d{2})\/RS\/(\d{4})/', $lastNo, $matches);
                if (!empty($matches) && $matches[1] == $currentYear && $matches[2] == $currentMonth) {
                    $newNo = $currentYear . '/' . $currentMonth . '/RS/' . str_pad(((int)$matches[3]) + 1, 4, '0', STR_PAD_LEFT);
                }
            }

            // Create return header
            DB::table('dbo.returnsales')->insert([
                'kodedivisi' => '01',
                'noretur' => $newNo,
                'tglretur' => $header['returnDate'],
                'kodecust' => $header['customer_id'],
                'total' => $total,
                'sisaretur' => $total,
                'keterangan' => $header['notes'] ?? '',
                'status' => 'Open'
            ]);

            // Create return details
            foreach ($items as $index => $item) {
                DB::table('dbo.returnsales_detail')->insert([
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

    public function getInvoices()
    {
        try {
            $invoices = DB::table('dbo.invoice')
                ->select([
                    'kodedivisi',
                    'noinvoice',
                    'tglfaktur',
                    'kodecust',
                    'total'
                ])
                ->where('sisainvoice', '>', 0)
                ->orderBy('tglfaktur', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->kodedivisi . '-' . $invoice->noinvoice,
                        'invoice_number' => $invoice->noinvoice,
                        'invoice_date' => $invoice->tglfaktur,
                        'customer_code' => $invoice->kodecust,
                        'total' => (float)($invoice->total ?? 0),
                        'display_text' => $invoice->noinvoice . ' - ' . $invoice->kodecust . ' (' . $invoice->tglfaktur . ')'
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve invoices: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getInvoiceDetails($invoiceNumber)
    {
        try {
            $details = DB::table('dbo.invoice_detail')
                ->join('dbo.invoice', function($join) {
                    $join->on('dbo.invoice_detail.kodedivisi', '=', 'dbo.invoice.kodedivisi')
                         ->on('dbo.invoice_detail.noinvoice', '=', 'dbo.invoice.noinvoice');
                })
                ->leftJoin('dbo.barang', 'dbo.invoice_detail.kodebarang', '=', 'dbo.barang.kodebarang')
                ->where('dbo.invoice_detail.noinvoice', $invoiceNumber)
                ->select([
                    'dbo.invoice_detail.kodebarang',
                    'dbo.barang.namabarang',
                    'dbo.invoice_detail.qty',
                    'dbo.invoice_detail.hargajual'
                ])
                ->get()
                ->map(function ($detail) {
                    return [
                        'code' => $detail->kodebarang,
                        'name' => $detail->namabarang ?? 'Unknown Product',
                        'qty_sold' => (float)($detail->qty ?? 0),
                        'price' => (float)($detail->hargajual ?? 0)
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve invoice details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomers()
    {
        try {
            $customers = DB::table('dbo.m_cust')
                ->select([
                    'kodedivisi',
                    'kodecust',
                    'namacust',
                    'alamat',
                    'telp'
                ])
                ->where('status', true)
                ->orderBy('namacust')
                ->get()
                ->map(function ($customer) {
                    return [
                        'id' => $customer->kodecust,
                        'code' => $customer->kodecust,
                        'name' => $customer->namacust,
                        'address' => $customer->alamat ?? '',
                        'phone' => $customer->telp ?? ''
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve customers: ' . $e->getMessage()
            ], 500);
        }
    }
}
