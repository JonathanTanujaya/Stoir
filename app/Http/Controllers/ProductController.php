<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getDiscount(Request $request)
    {
        try {
            $diskon = $this->productService->getDiskon(
                $request->input('KodeDivisi'),
                $request->input('KodeCust'),
                $request->input('KodeBarang'),
                $request->input('QtyPengambilan')
            );
            return response()->json(['diskon' => $diskon]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDiscountDetail(Request $request)
    {
        try {
            $diskonDetail = $this->productService->getDiskonDetail(
                $request->input('KodeDivisi'),
                $request->input('KodeCust'),
                $request->input('KodeBarang'),
                $request->input('QtyPengambilan')
            );
            return response()->json(['diskon_detail' => $diskonDetail]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getStokClaim(Request $request)
    {
        try {
            $stokClaim = $this->productService->getStokClaim(
                $request->input('KodeDivisi'),
                $request->input('KodeBarang')
            );
            return response()->json(['stok_claim' => $stokClaim]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
