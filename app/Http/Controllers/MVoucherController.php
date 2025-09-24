<?php

namespace App\Http\Controllers;

use App\Models\MVoucher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MVoucherController extends Controller
{
    public function index(): JsonResponse
    {
        $mVouchers = MVoucher::with(['sales', 'dVouchers'])->get();
        return response()->json($mVouchers);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_voucher' => 'required|string|max:20',
            'tgl_voucher' => 'required|date',
            'kode_sales' => 'required|string|max:10',
            'nilai' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        $mVoucher = MVoucher::create($request->all());
        return response()->json($mVoucher, 201);
    }

    public function show(string $noVoucher): JsonResponse
    {
        $mVoucher = MVoucher::with(['sales', 'dVouchers'])
            ->where('no_voucher', $noVoucher)
            ->firstOrFail();
        return response()->json($mVoucher);
    }

    public function edit(string $noVoucher)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $noVoucher): JsonResponse
    {
        $request->validate([
            'tgl_voucher' => 'required|date',
            'kode_sales' => 'required|string|max:10',
            'nilai' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        $mVoucher = MVoucher::where('no_voucher', $noVoucher)
            ->firstOrFail();
        
        $mVoucher->update($request->all());
        return response()->json($mVoucher);
    }

    public function destroy(string $noVoucher): JsonResponse
    {
        $mVoucher = MVoucher::where('no_voucher', $noVoucher)
            ->firstOrFail();
        
        $mVoucher->delete();
        return response()->json(['message' => 'MVoucher deleted successfully']);
    }
}
