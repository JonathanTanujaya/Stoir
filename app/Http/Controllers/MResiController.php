<?php

namespace App\Http\Controllers;

use App\Models\MResi;
use App\Models\DBank;
use App\Models\MBank;
use App\Models\MCust;
use Illuminate\Http\Request;

class MResiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $items = MResi::orderBy('tglpembayaran', 'desc')->get()->map(fn($r)=>[
                'kodeDivisi' => $r->kodedivisi,
                'noResi' => $r->noresi,
                'tglPembayaran' => $r->tglpembayaran,
                'kodeCustomer' => $r->kodecust,
                'jumlah' => (float)$r->jumlah,
                'sisaResi' => (float)$r->sisaresi,
                'keterangan' => $r->keterangan,
                'status' => $r->status
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $items,
                'totalCount' => $items->count(),
                'message' => 'M Resi retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve m resi data',
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
                'kodeDivisi' => 'required|string|max:2',
                'noResi' => 'required|string|max:20',
                'tglPembayaran' => 'required|date',
                'kodeCustomer' => 'required|string|max:10',
                'jumlah' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            $mresi = MResi::create([
                'kodedivisi' => $request->kodeDivisi,
                'noresi' => $request->noResi,
                'tglpembayaran' => $request->tglPembayaran,
                'kodecust' => $request->kodeCustomer,
                'jumlah' => $request->jumlah,
                'sisaresi' => $request->jumlah, // initially same as jumlah
                'keterangan' => $request->keterangan,
                'status' => 1
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'kodeDivisi' => $mresi->kodedivisi,
                    'noResi' => $mresi->noresi,
                    'tglPembayaran' => $mresi->tglpembayaran,
                    'kodeCustomer' => $mresi->kodecust,
                    'jumlah' => (float)$mresi->jumlah,
                    'sisaResi' => (float)$mresi->sisaresi,
                    'keterangan' => $mresi->keterangan,
                    'status' => $mresi->status
                ],
                'message' => 'M Resi created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create m resi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $noResi)
    {
        try {
            $mresi = MResi::where('kodedivisi', $kodeDivisi)
                         ->where('noresi', $noResi)
                         ->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'kodeDivisi' => $mresi->kodedivisi,
                    'noResi' => $mresi->noresi,
                    'tglPembayaran' => $mresi->tglpembayaran,
                    'kodeCustomer' => $mresi->kodecust,
                    'jumlah' => (float)$mresi->jumlah,
                    'sisaResi' => (float)$mresi->sisaresi,
                    'keterangan' => $mresi->keterangan,
                    'status' => $mresi->status
                ],
                'message' => 'M Resi retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'M Resi not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $noResi)
    {
        try {
            $mresi = MResi::where('kodedivisi', $kodeDivisi)
                         ->where('noresi', $noResi)
                         ->firstOrFail();
            
            $request->validate([
                'tglPembayaran' => 'required|date',
                'kodeCustomer' => 'required|string|max:10',
                'jumlah' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            $mresi->update([
                'tglpembayaran' => $request->tglPembayaran,
                'kodecust' => $request->kodeCustomer,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'kodeDivisi' => $mresi->kodedivisi,
                    'noResi' => $mresi->noresi,
                    'tglPembayaran' => $mresi->tglpembayaran,
                    'kodeCustomer' => $mresi->kodecust,
                    'jumlah' => (float)$mresi->jumlah,
                    'sisaResi' => (float)$mresi->sisaresi,
                    'keterangan' => $mresi->keterangan,
                    'status' => $mresi->status
                ],
                'message' => 'M Resi updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update m resi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $noResi)
    {
        try {
            $mresi = MResi::where('kodedivisi', $kodeDivisi)
                         ->where('noresi', $noResi)
                         ->firstOrFail();
            
            $mresi->delete();

            return response()->json([
                'success' => true,
                'message' => 'M Resi deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete m resi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVCustomerResi()
    {
        $vCustomerResi = DBank::rightJoin('m_resi', function ($join) {
            $join->on('d_bank.KodeDivisi', '=', 'm_resi.KodeDivisi')
                 ->on('d_bank.NoRekening', '=', 'm_resi.NoRekeningTujuan');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('m_resi.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('m_resi.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->leftJoin('m_bank', function ($join) {
            $join->on('d_bank.KodeDivisi', '=', 'm_bank.KodeDivisi')
                 ->on('d_bank.KodeBank', '=', 'm_bank.KodeBank');
        })
        ->select(
            'm_resi.KodeDivisi',
            'm_resi.NoResi',
            'm_resi.NoRekeningTujuan',
            'm_resi.TglPembayaran',
            'm_resi.KodeCust',
            'm_cust.NamaCust',
            'm_resi.Jumlah',
            'm_resi.SisaResi',
            'm_resi.Keterangan',
            'm_resi.Status',
            'd_bank.KodeBank',
            'm_bank.NamaBank'
        )
        ->get();

        return response()->json($vCustomerResi);
    }
}
