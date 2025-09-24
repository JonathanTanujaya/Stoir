<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceDetailRequest;
use App\Http\Requests\UpdateInvoiceDetailRequest;
use App\Http\Resources\InvoiceDetailCollection;
use App\Http\Resources\InvoiceDetailResource;
use App\Models\InvoiceDetail;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InvoiceDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $noInvoice): JsonResponse
    {
        // Start timing for meta and normalize route params
        $request->attributes->set('query_start_time', microtime(true));
        $noInvoice = strtoupper($noInvoice);

        // Verify that the parent invoice exists
        $invoice = Invoice::where('no_invoice', $noInvoice)->firstOrFail();

        $query = InvoiceDetail::where('no_invoice', $noInvoice);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $driver = DB::connection()->getDriverName();
            $op = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where(function($q) use ($search, $op) {
                $q->where('kode_barang', $op, "%{$search}%")
                  ->orWhere('jenis', $op, "%{$search}%")
                  ->orWhere('status', $op, "%{$search}%");
            });
        }

        // Filter by specific fields
        if ($request->filled('kode_barang')) {
            $driver = DB::connection()->getDriverName();
            $op = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where('kode_barang', $op, "%{$request->get('kode_barang')}%");
        }

        if ($request->filled('jenis')) {
            $driver = DB::connection()->getDriverName();
            $op = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where('jenis', $op, "%{$request->get('jenis')}%");
        }

        if ($request->filled('status')) {
            $driver = DB::connection()->getDriverName();
            $op = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where('status', $op, "%{$request->get('status')}%");
        }

        // Price range filters
        if ($request->filled('min_harga')) {
            $query->where('harga_jual', '>=', $request->get('min_harga'));
        }

        if ($request->filled('max_harga')) {
            $query->where('harga_jual', '<=', $request->get('max_harga'));
        }

        // Quantity range filters
        if ($request->filled('min_qty')) {
            $query->where('qty_supply', '>=', $request->get('min_qty'));
        }

        if ($request->filled('max_qty')) {
            $query->where('qty_supply', '<=', $request->get('max_qty'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['id', 'kode_barang', 'qty_supply', 'harga_jual', 'harga_nett', 'jenis', 'status'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Load relationships
        $query->with(['invoice', 'barang']);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $invoiceDetails = $query->paginate($perPage);

        return (new InvoiceDetailCollection($invoiceDetails))
            ->additional([
                'success' => true,
                'parent' => [
                    'no_invoice' => $noInvoice,
                ]
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceDetailRequest $request, string $noInvoice): JsonResponse
    {
        $noInvoice = strtoupper($noInvoice);
        // Verify that the parent invoice exists
        $invoice = Invoice::where('no_invoice', $noInvoice)->firstOrFail();

        $validated = $request->validated();
        $validated['no_invoice'] = $noInvoice;

        $invoiceDetail = InvoiceDetail::create($validated);
        $invoiceDetail->load(['invoice', 'barang']);

        return response()->json(new InvoiceDetailResource($invoiceDetail), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $noInvoice, int $id): JsonResponse
    {
        $noInvoice = strtoupper($noInvoice);
        $invoiceDetail = InvoiceDetail::where([
            'id' => $id,
            'no_invoice' => $noInvoice
        ])->with(['invoice', 'barang'])->firstOrFail();

        return response()->json(new InvoiceDetailResource($invoiceDetail));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceDetailRequest $request, string $noInvoice, int $id): JsonResponse
    {
        $noInvoice = strtoupper($noInvoice);
        $invoiceDetail = InvoiceDetail::where([
            'id' => $id,
            'no_invoice' => $noInvoice
        ])->firstOrFail();

        $validated = $request->validated();
        $invoiceDetail->update($validated);
        $invoiceDetail->load(['invoice', 'barang']);

        return response()->json(new InvoiceDetailResource($invoiceDetail));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $noInvoice, int $id): Response
    {
        $noInvoice = strtoupper($noInvoice);
        $invoiceDetail = InvoiceDetail::where([
            'id' => $id,
            'no_invoice' => $noInvoice
        ])->firstOrFail();

        $invoiceDetail->delete();

        return response(null, 204);
    }

    /**
     * Get invoice details statistics for the specified invoice.
     */
    public function stats(string $noInvoice): JsonResponse
    {
        $noInvoice = strtoupper($noInvoice);
        // Verify that the parent invoice exists
        $invoice = Invoice::where('no_invoice', $noInvoice)->firstOrFail();

        $stats = [
            'total_items' => InvoiceDetail::where('no_invoice', $noInvoice)->count(),
            
            'total_quantity' => InvoiceDetail::where('no_invoice', $noInvoice)->sum('qty_supply'),
            
            'total_gross_amount' => InvoiceDetail::where('no_invoice', $noInvoice)->sum(DB::raw('qty_supply * harga_jual')),
            
            'total_net_amount' => InvoiceDetail::where('no_invoice', $noInvoice)->sum('harga_nett'),
            
            'average_unit_price' => InvoiceDetail::where('no_invoice', $noInvoice)->avg('harga_jual'),
            
            'items_by_status' => InvoiceDetail::where('no_invoice', $noInvoice)->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
                
            'items_by_type' => InvoiceDetail::where('no_invoice', $noInvoice)->selectRaw('jenis, COUNT(*) as count')
                ->groupBy('jenis')
                ->pluck('count', 'jenis')
                ->toArray()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
