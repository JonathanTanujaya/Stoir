<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartPenerimaanRequest;
use App\Models\PartPenerimaan;
use App\Models\PartPenerimaanDetail;
use App\Models\MasterSupplier;
use App\Models\MasterBarang;
use App\Models\MasterDivisi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartPenerimaanController extends Controller
{
    /**
     * Get list of Part Penerimaan with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PartPenerimaan::with(['supplier', 'divisi']);

            // Apply filters
            if ($request->filled('kode_divisi')) {
                $query->where('kode_divisi', $request->kode_divisi);
            }

            if ($request->filled('kode_supplier')) {
                $query->where('kode_supplier', $request->kode_supplier);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tgl_penerimaan', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            if ($request->filled('no_penerimaan')) {
                $query->where('no_penerimaan', 'ILIKE', '%' . $request->no_penerimaan . '%');
            }

            if ($request->filled('no_faktur')) {
                $query->where('no_faktur', 'ILIKE', '%' . $request->no_faktur . '%');
            }

            // Sorting
            $sortField = $request->get('sort_field', 'tgl_penerimaan');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $penerimaan = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $penerimaan,
                'message' => 'Data Part Penerimaan berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting Part Penerimaan list: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data Part Penerimaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific Part Penerimaan with details
     */
    public function show(string $kodeDivisi, string $noPenerimaan): JsonResponse
    {
        try {
            $penerimaan = PartPenerimaan::with([
                'supplier',
                'divisi',
                'details.barang'
            ])
            ->where('kode_divisi', $kodeDivisi)
            ->where('no_penerimaan', $noPenerimaan)
            ->first();

            if (!$penerimaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part Penerimaan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $penerimaan,
                'message' => 'Data Part Penerimaan berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting Part Penerimaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data Part Penerimaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new Part Penerimaan using stored procedure
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'kode_divisi' => 'required|string|max:10',
                'no_penerimaan' => 'required|string|max:20',
                'tgl_penerimaan' => 'required|date',
                'kode_supplier' => 'required|string|max:10',
                'jatuh_tempo' => 'required|date',
                'no_faktur' => 'required|string|max:20',
                'kode_valas' => 'nullable|string|max:3',
                'kurs' => 'nullable|numeric|min:0',
                'pajak_persen' => 'nullable|numeric|min:0|max:100',
                'details' => 'required|array|min:1',
                'details.*.kode_barang' => 'required|string|max:20',
                'details.*.qty_supply' => 'required|integer|min:1',
                'details.*.harga' => 'required|numeric|min:0',
                'details.*.diskon1' => 'nullable|numeric|min:0|max:100',
                'details.*.diskon2' => 'nullable|numeric|min:0|max:100'
            ]);

            DB::beginTransaction();

            $validated = $request->all();
            
            // Calculate totals from details
            $totalBruto = 0;
            $totalDiscount = 0;
            
            foreach ($validated['details'] as $detail) {
                $subtotal = $detail['qty_supply'] * $detail['harga'];
                $totalBruto += $subtotal;
                
                // Calculate discount
                $disc1Amount = $subtotal * ($detail['diskon1'] ?? 0) / 100;
                $afterDisc1 = $subtotal - $disc1Amount;
                $disc2Amount = $afterDisc1 * ($detail['diskon2'] ?? 0) / 100;
                $totalDiscount += ($disc1Amount + $disc2Amount);
            }

            $totalNetto = $totalBruto - $totalDiscount;
            $pajakAmount = $totalNetto * ($validated['pajak_persen'] ?? 0) / 100;
            $grandTotal = $totalNetto + $pajakAmount;

            // Create main record manually (without stored procedure for now)
            $penerimaan = PartPenerimaan::create([
                'kode_divisi' => $validated['kode_divisi'],
                'no_penerimaan' => $validated['no_penerimaan'],
                'tgl_penerimaan' => $validated['tgl_penerimaan'],
                'kode_valas' => $validated['kode_valas'] ?? 'IDR',
                'kurs' => $validated['kurs'] ?? 1,
                'kode_supplier' => $validated['kode_supplier'],
                'jatuh_tempo' => $validated['jatuh_tempo'],
                'no_faktur' => $validated['no_faktur'],
                'total' => $totalBruto,
                'discount' => min($totalDiscount, 999.99), // Sesuai constraint DB
                'pajak' => min($pajakAmount, 999.99), // Sesuai constraint DB
                'grand_total' => $grandTotal,
                'status' => 'Open'
            ]);

            // Create detail records
            foreach ($validated['details'] as $detail) {
                $hargaNett = $detail['harga'] * (1 - ($detail['diskon1'] ?? 0) / 100) * (1 - ($detail['diskon2'] ?? 0) / 100);
                
                PartPenerimaanDetail::create([
                    'kode_divisi' => $validated['kode_divisi'],
                    'no_penerimaan' => $validated['no_penerimaan'],
                    'kode_barang' => $detail['kode_barang'],
                    'qty_supply' => $detail['qty_supply'],
                    'harga' => $detail['harga'],
                    'diskon1' => $detail['diskon1'] ?? 0,
                    'diskon2' => $detail['diskon2'] ?? 0,
                    'harga_nett' => $hargaNett
                ]);

                // Update stock (simplified version)
                $existingStock = DB::table('d_barang')
                    ->where('kode_divisi', $validated['kode_divisi'])
                    ->where('kode_barang', $detail['kode_barang'])
                    ->where('modal', $hargaNett)
                    ->first();

                if ($existingStock) {
                    DB::table('d_barang')
                        ->where('id', $existingStock->id)
                        ->increment('stok', $detail['qty_supply']);
                } else {
                    DB::table('d_barang')->insert([
                        'kode_divisi' => $validated['kode_divisi'],
                        'kode_barang' => $detail['kode_barang'],
                        'modal' => $hargaNett,
                        'tgl_masuk' => now(),
                        'stok' => $detail['qty_supply']
                    ]);
                }

                // Insert into kartu_stok
                $totalStock = DB::table('d_barang')
                    ->where('kode_divisi', $validated['kode_divisi'])
                    ->where('kode_barang', $detail['kode_barang'])
                    ->sum('stok');

                DB::table('kartu_stok')->insert([
                    'kode_divisi' => $validated['kode_divisi'],
                    'kode_barang' => $detail['kode_barang'],
                    'no_ref' => $validated['no_penerimaan'],
                    'tgl_proses' => now(),
                    'tipe' => 'Pembelian',
                    'increase' => $detail['qty_supply'],
                    'decrease' => 0,
                    'harga_debet' => $hargaNett,
                    'harga_kredit' => 0,
                    'qty' => $totalStock,
                    'hpp' => $hargaNett
                ]);
            }

            DB::commit();

            // Get the created record with relationships
            $penerimaan = PartPenerimaan::with(['supplier', 'details.barang'])
                ->where('kode_divisi', $validated['kode_divisi'])
                ->where('no_penerimaan', $validated['no_penerimaan'])
                ->first();

            return response()->json([
                'success' => true,
                'data' => $penerimaan,
                'message' => 'Part Penerimaan berhasil dibuat'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Part Penerimaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Part Penerimaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Part Penerimaan status
     */
    public function updateStatus(Request $request, string $kodeDivisi, string $noPenerimaan): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:Open,Finish,Cancel'
            ]);

            $penerimaan = PartPenerimaan::where('kode_divisi', $kodeDivisi)
                ->where('no_penerimaan', $noPenerimaan)
                ->first();

            if (!$penerimaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part Penerimaan tidak ditemukan'
                ], 404);
            }

            if ($penerimaan->status === 'Cancel') {
                return response()->json([
                    'success' => false,
                    'message' => 'Part Penerimaan yang sudah dibatalkan tidak dapat diubah'
                ], 422);
            }

            $penerimaan->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'data' => $penerimaan,
                'message' => 'Status Part Penerimaan berhasil diubah'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating Part Penerimaan status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status Part Penerimaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get suppliers for dropdown
     */
    public function getSuppliers(Request $request): JsonResponse
    {
        try {
            $query = MasterSupplier::select('kode_divisi', 'kode_supplier', 'nama_supplier')
                ->where('status', true);

            if ($request->filled('kode_divisi')) {
                $query->where('kode_divisi', $request->kode_divisi);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('kode_supplier', 'ILIKE', '%' . $search . '%')
                      ->orWhere('nama_supplier', 'ILIKE', '%' . $search . '%');
                });
            }

            $suppliers = $query->orderBy('nama_supplier')->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting suppliers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products for dropdown
     */
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $query = MasterBarang::select(
                'kode_divisi', 
                'kode_barang', 
                'nama_barang', 
                'satuan',
                'merk',
                'harga_list'
            )->where('status', true);

            if ($request->filled('kode_divisi')) {
                $query->where('kode_divisi', $request->kode_divisi);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('kode_barang', 'ILIKE', '%' . $search . '%')
                      ->orWhere('nama_barang', 'ILIKE', '%' . $search . '%')
                      ->orWhere('barcode', 'ILIKE', '%' . $search . '%');
                });
            }

            $products = $query->orderBy('nama_barang')->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate document number
     */
    public function generateNumber(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'kode_divisi' => 'required|string'
            ]);

            $kodeDivisi = $request->kode_divisi;
            $kodeDoc = 'PPN'; // Part Penerimaan

            // Call stored procedure to generate number
            $nomorBaru = null;
            DB::transaction(function () use ($kodeDivisi, $kodeDoc, &$nomorBaru) {
                DB::statement('CALL sp_set_nomor(?, ?, ?)', [$kodeDivisi, $kodeDoc, $nomorBaru]);
                
                // Get the generated number from m_dokumen
                $result = DB::select('SELECT nomor FROM m_dokumen WHERE kode_divisi = ? AND kode_dok = ?', [$kodeDivisi, $kodeDoc]);
                $nomorBaru = $result[0]->nomor ?? null;
            });

            if (!$nomorBaru) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal generate nomor dokumen'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => ['no_penerimaan' => $nomorBaru],
                'message' => 'Nomor dokumen berhasil digenerate'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating document number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Part Penerimaan summary/statistics
     */
    public function getSummary(Request $request): JsonResponse
    {
        try {
            $query = PartPenerimaan::query();

            if ($request->filled('kode_divisi')) {
                $query->where('kode_divisi', $request->kode_divisi);
            }

            if ($request->filled('periode')) {
                $periode = $request->periode;
                if ($periode === 'today') {
                    $query->whereDate('tgl_penerimaan', today());
                } elseif ($periode === 'this_month') {
                    $query->whereMonth('tgl_penerimaan', now()->month)
                          ->whereYear('tgl_penerimaan', now()->year);
                } elseif ($periode === 'this_year') {
                    $query->whereYear('tgl_penerimaan', now()->year);
                }
            }

            $summary = [
                'total_transaksi' => (clone $query)->count(),
                'total_nilai' => (clone $query)->sum('grand_total'),
                'status_open' => (clone $query)->where('status', 'Open')->count(),
                'status_finish' => (clone $query)->where('status', 'Finish')->count(),
                'status_cancel' => (clone $query)->where('status', 'Cancel')->count(),
                'rata_rata_nilai' => (clone $query)->avg('grand_total') ?? 0
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting Part Penerimaan summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
