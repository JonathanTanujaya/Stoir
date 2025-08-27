<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClaimService;
use App\Models\ClaimPenjualan;
use App\Models\ClaimPenjualanDetail;
use App\Models\MBarang;
use App\Models\MCust;

class ClaimController extends Controller
{
    protected $claimService;

    public function __construct(ClaimService $claimService)
    {
        $this->claimService = $claimService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $claims = ClaimPenjualan::all();
        return response()->json(['data' => $claims]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->claimService->createClaim(
                $request->input('KodeDivisi'),
                $request->input('NoClaim'),
                $request->input('KodeCust'),
                $request->input('Ket'),
                $request->input('NoInvoice'),
                $request->input('KodeBarang'),
                $request->input('QtyClaim')
            );

            return response()->json(['message' => 'Claim created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getVClaimDetail()
    {
        $vClaimDetail = ClaimPenjualanDetail::leftJoin('claim_penjualan', function ($join) {
            $join->on('claim_penjualan_detail.KodeDivisi', '=', 'claim_penjualan.KodeDivisi')
                 ->on('claim_penjualan_detail.NoClaim', '=', 'claim_penjualan.NoClaim');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('claim_penjualan.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('claim_penjualan.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->select(
            'claim_penjualan.KodeDivisi',
            'claim_penjualan.NoClaim',
            'claim_penjualan.TglClaim',
            'claim_penjualan.KodeCust',
            'claim_penjualan.Keterangan',
            'claim_penjualan_detail.NoInvoice',
            'claim_penjualan_detail.KodeBarang',
            'claim_penjualan_detail.QtyClaim',
            'claim_penjualan_detail.ID',
            'claim_penjualan_detail.Status',
            'm_cust.NamaCust'
        )
        ->get();

        return response()->json($vClaimDetail);
    }

    public function getVCustClaim()
    {
        $vCustClaim = ClaimPenjualan::leftJoin('m_cust', function ($join) {
            $join->on('claim_penjualan.KodeCust', '=', 'm_cust.KodeCust')
                 ->on('claim_penjualan.KodeDivisi', '=', 'm_cust.KodeDivisi');
        })
        ->select(
            'claim_penjualan.KodeDivisi',
            'claim_penjualan.NoClaim',
            'claim_penjualan.TglClaim',
            'claim_penjualan.KodeCust',
            'm_cust.NamaCust',
            'claim_penjualan.Keterangan',
            'claim_penjualan.Status'
        )
        ->get();

        return response()->json($vCustClaim);
    }
}
