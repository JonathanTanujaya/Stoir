<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanFinance;
use App\Models\MCust;
use App\Models\PenerimaanFinanceDetail;
use App\Models\Invoice;
use App\Models\MSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanFinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Sementara tanpa relasi untuk menghindari composite key error
            $penerimaanFinances = PenerimaanFinance::orderBy('tglpenerimaan', 'desc')
                                                 ->limit(5)
                                                 ->get();
            $data = $penerimaanFinances->map(fn($p)=>[
                'kodeDivisi' => $p->kodedivisi,
                'noPenerimaan' => $p->nopenerimaan,
                'tglPenerimaan' => $p->tglpenerimaan,
                'kodeCustomer' => $p->kodecust,
                'kodeSales' => $p->kodesales,
                'total' => (float)$p->total,
                'status' => $p->status
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Data penerimaan finance retrieved successfully (limited to 5 for testing)',
                'data' => $data,
                'totalCount' => $data->count(),
                'note' => 'Data limited to 5 records for Laravel testing - Relations disabled temporarily'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve penerimaan finance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'kodedivisi' => 'required|string|max:2',
                'nopenerimaan' => 'required|string|max:20',
                'tglpenerimaan' => 'required|date',
                'kodecust' => 'required|string|max:10',
                'kodesales' => 'required|string|max:10',
                'total' => 'required|numeric|min:0',
                'details' => 'required|array',
                'details.*.noinvoice' => 'required|string|max:20',
                'details.*.jumlah' => 'required|numeric|min:0'
            ]);

            $penerimaanFinance = PenerimaanFinance::create($request->only([
                'kodedivisi', 'nopenerimaan', 'tglpenerimaan', 'kodecust', 'kodesales', 'total'
            ]));

            // Create details
            foreach ($request->details as $detail) {
                PenerimaanFinanceDetail::create([
                    'kodedivisi' => $request->kodedivisi,
                    'nopenerimaan' => $request->nopenerimaan,
                    'noinvoice' => $detail['noinvoice'],
                    'jumlah' => $detail['jumlah']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Penerimaan finance created successfully',
                'data' => $penerimaanFinance->load('details')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create penerimaan finance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $noPenerimaan)
    {
        try {
            $penerimaanFinance = PenerimaanFinance::with(['customer', 'sales', 'details.invoice'])
                                                ->where('kodedivisi', $kodeDivisi)
                                                ->where('nopenerimaan', $noPenerimaan)
                                                ->first();
            
            if (!$penerimaanFinance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerimaan finance not found'
                ], 404);
            }

            $p = $penerimaanFinance;
            return response()->json([
                'success' => true,
                'message' => 'Penerimaan finance retrieved successfully',
                'data' => [
                    'kodeDivisi' => $p->kodedivisi,
                    'noPenerimaan' => $p->nopenerimaan,
                    'tglPenerimaan' => $p->tglpenerimaan,
                    'kodeCustomer' => $p->kodecust,
                    'kodeSales' => $p->kodesales,
                    'total' => (float)$p->total,
                    'status' => $p->status,
                    'details' => $p->details->map(fn($d)=>[
                        'noInvoice' => $d->noinvoice,
                        'jumlah' => (float)$d->jumlah
                    ])
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve penerimaan finance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $noPenerimaan)
    {
        try {
            $penerimaanFinance = PenerimaanFinance::where('kodedivisi', $kodeDivisi)
                                                ->where('nopenerimaan', $noPenerimaan)
                                                ->first();
            
            if (!$penerimaanFinance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerimaan finance not found'
                ], 404);
            }

            $request->validate([
                'tglpenerimaan' => 'required|date',
                'kodecust' => 'required|string|max:10',
                'kodesales' => 'required|string|max:10',
                'total' => 'required|numeric|min:0'
            ]);

            $penerimaanFinance->update($request->only(['tglpenerimaan', 'kodecust', 'kodesales', 'total']));

            $p = $penerimaanFinance;
            return response()->json([
                'success' => true,
                'message' => 'Penerimaan finance updated successfully',
                'data' => [
                    'kodeDivisi' => $p->kodedivisi,
                    'noPenerimaan' => $p->nopenerimaan,
                    'tglPenerimaan' => $p->tglpenerimaan,
                    'kodeCustomer' => $p->kodecust,
                    'kodeSales' => $p->kodesales,
                    'total' => (float)$p->total,
                    'status' => $p->status
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update penerimaan finance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $noPenerimaan)
    {
        try {
            $penerimaanFinance = PenerimaanFinance::where('kodedivisi', $kodeDivisi)
                                                ->where('nopenerimaan', $noPenerimaan)
                                                ->first();
            
            if (!$penerimaanFinance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerimaan finance not found'
                ], 404);
            }

            // Delete details first
            PenerimaanFinanceDetail::where('kodedivisi', $kodeDivisi)
                                  ->where('nopenerimaan', $noPenerimaan)
                                  ->delete();

            $penerimaanFinance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penerimaan finance deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete penerimaan finance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVPenerimaanFinance()
    {
        $vPenerimaanFinance = PenerimaanFinance::leftJoin('m_cust', function ($join) {
            $join->on('penerimaan_finance.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('penerimaan_finance.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->select(
            'penerimaan_finance.KodeDivisi',
            'penerimaan_finance.NoPenerimaan',
            'penerimaan_finance.TglPenerimaan',
            'penerimaan_finance.Tipe',
            'penerimaan_finance.NoRef',
            'penerimaan_finance.TglRef',
            'penerimaan_finance.TglPencairan',
            'penerimaan_finance.BankRef',
            'penerimaan_finance.NoRekTujuan',
            'penerimaan_finance.KodeCust',
            'penerimaan_finance.Jumlah',
            'penerimaan_finance.Status',
            'm_cust.NamaCust'
        )
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Penerimaan Finance view data retrieved successfully',
            'data' => $vPenerimaanFinance
        ]);
    }

    /**
     * Get all penerimaan finance for frontend (no limit)
     */
    public function getAllForFrontend()
    {
        try {
            // Sementara tanpa relasi untuk menghindari composite key error
            $penerimaanFinances = PenerimaanFinance::orderBy('tglpenerimaan', 'desc')
                                                 ->get();
            
            $data = $penerimaanFinances->map(fn($p)=>[
                'kodeDivisi' => $p->kodedivisi,
                'noPenerimaan' => $p->nopenerimaan,
                'tglPenerimaan' => $p->tglpenerimaan,
                'kodeCustomer' => $p->kodecust,
                'kodeSales' => $p->kodesales,
                'total' => (float)$p->total,
                'status' => $p->status
            ]);
            return response()->json([
                'success' => true,
                'message' => 'All penerimaan finance data retrieved for frontend',
                'data' => $data,
                'totalCount' => $data->count(),
                'note' => 'Relations disabled temporarily to fix composite key error'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve all penerimaan finance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVPenerimaanFinanceDetail()
    {
        $vPenerimaanFinanceDetail = PenerimaanFinanceDetail::leftJoin('invoice', function ($join) {
            $join->on('penerimaan_finance_detail.KodeDivisi', '=', 'invoice.KodeDivisi')
                 ->on('penerimaan_finance_detail.Noinvoice', '=', 'invoice.NoInvoice');
        })
        ->leftJoin('m_sales', function ($join) {
            $join->on('m_sales.KodeSales', '=', 'invoice.KodeSales')
                 ->on('m_sales.KodeDivisi', '=', 'invoice.KodeDivisi');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('m_cust.KodeDivisi', '=', 'invoice.KodeDivisi')
                 ->on('m_cust.KodeCust', '=', 'invoice.KodeCust');
        })
        ->leftJoin('penerimaan_finance', function ($join) {
            $join->on('penerimaan_finance_detail.KodeDivisi', '=', 'penerimaan_finance.KodeDivisi')
                 ->on('penerimaan_finance_detail.NoPenerimaan', '=', 'penerimaan_finance.NoPenerimaan');
        })
        ->select(
            'penerimaan_finance.KodeDivisi',
            'penerimaan_finance.NoPenerimaan',
            'penerimaan_finance.TglPenerimaan',
            'penerimaan_finance.Tipe',
            'penerimaan_finance.NoRef',
            'penerimaan_finance.TglRef',
            'penerimaan_finance.TglPencairan',
            'penerimaan_finance.BankRef',
            'penerimaan_finance.NoRekTujuan',
            'penerimaan_finance.KodeCust',
            'penerimaan_finance.Jumlah',
            'penerimaan_finance.Status',
            'penerimaan_finance_detail.Noinvoice',
            'penerimaan_finance_detail.JumlahInvoice',
            'penerimaan_finance_detail.JumlahBayar',
            'penerimaan_finance_detail.JumlahDispensasi',
            DB::raw('penerimaan_finance_detail.Status AS StatusDetail'),
            'penerimaan_finance_detail.id',
            'invoice.SisaInvoice',
            DB::raw('penerimaan_finance_detail.SisaInvoice - penerimaan_finance_detail.JumlahBayar - penerimaan_finance_detail.JumlahDispensasi AS SisaBayar'),
            'm_cust.NamaCust',
            'invoice.KodeSales',
            'm_sales.NamaSales',
            'penerimaan_finance.NoVoucher'
        )
        ->get();

        return response()->json($vPenerimaanFinanceDetail);
    }
}
