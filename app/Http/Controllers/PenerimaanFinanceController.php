<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenerimaanFinanceRequest;
use App\Http\Requests\UpdatePenerimaanFinanceRequest;
use App\Http\Resources\PenerimaanFinanceCollection;
use App\Http\Resources\PenerimaanFinanceResource;
use App\Models\PenerimaanFinance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PenerimaanFinanceController extends Controller
{
    /**
     * Display a listing of penerimaan finance with pagination and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PenerimaanFinance::with(['customer', 'penerimaanFinanceDetails']);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $driver = DB::connection()->getDriverName();
                $searchOperator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
                $query->where(function ($q) use ($search, $searchOperator) {
                    $q->where('no_penerimaan', $searchOperator, "%{$search}%")
                        ->orWhere('no_ref', $searchOperator, "%{$search}%")
                        ->orWhere('keterangan', $searchOperator, "%{$search}%")
                        ->orWhere('kode_cust', $searchOperator, "%{$search}%");
                });
            }

            // Apply date filter
            if ($request->filled('date_from')) {
                $query->where('tgl_penerimaan', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->where('tgl_penerimaan', '<=', $request->get('date_to'));
            }

            // Apply customer filter
            if ($request->filled('kode_cust')) {
                $query->where('kode_cust', $request->get('kode_cust'));
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            // Apply type filter
            if ($request->filled('tipe')) {
                $query->where('tipe', $request->get('tipe'));
            }

            // Apply amount range filter
            if ($request->filled('min_amount')) {
                $query->where('jumlah', '>=', $request->get('min_amount'));
            }

            if ($request->filled('max_amount')) {
                $query->where('jumlah', '<=', $request->get('max_amount'));
            }

            // Apply sorting
            $sortField = $request->get('sort', 'no_penerimaan');
            $sortDirection = $request->get('direction', 'desc');

            $allowedSortFields = ['no_penerimaan', 'tgl_penerimaan', 'jumlah', 'status', 'kode_cust'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }

            // Paginate results
            $perPage = min($request->get('per_page', 15), 100);
            $penerimaanFinances = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => new PenerimaanFinanceCollection($penerimaanFinances),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penerimaan finance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created penerimaan finance.
     */
    public function store(StorePenerimaanFinanceRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $penerimaanFinanceData = $request->validated();

            // Set default status if not provided
            if (! isset($penerimaanFinanceData['status'])) {
                $penerimaanFinanceData['status'] = 'Open';
            }

            $penerimaanFinance = PenerimaanFinance::create($penerimaanFinanceData);
            $penerimaanFinance->refresh()->load('customer');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penerimaan finance berhasil dibuat',
                'data' => new PenerimaanFinanceResource($penerimaanFinance),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat penerimaan finance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified penerimaan finance.
     */
    public function show(string $noPenerimaan): JsonResponse
    {
        try {
            $penerimaanFinance = PenerimaanFinance::with(['customer', 'penerimaanFinanceDetails'])
                ->where('no_penerimaan', $noPenerimaan)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => new PenerimaanFinanceResource($penerimaanFinance),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penerimaan finance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified penerimaan finance.
     */
    public function update(UpdatePenerimaanFinanceRequest $request, string $noPenerimaan): JsonResponse
    {
        try {
            $penerimaanFinance = PenerimaanFinance::where('no_penerimaan', $noPenerimaan)
                ->firstOrFail();

            DB::beginTransaction();

            $penerimaanFinance->update($request->validated());
            $penerimaanFinance->refresh()->load('customer');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penerimaan finance berhasil diperbarui',
                'data' => new PenerimaanFinanceResource($penerimaanFinance),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui penerimaan finance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified penerimaan finance.
     */
    public function destroy(string $noPenerimaan): JsonResponse
    {
        try {
            $penerimaanFinance = PenerimaanFinance::where('no_penerimaan', $noPenerimaan)
                ->firstOrFail();

            DB::beginTransaction();

            // Check if has related details
            $hasDetails = $penerimaanFinance->penerimaanFinanceDetails()->exists();
            if ($hasDetails) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Penerimaan finance tidak dapat dihapus karena memiliki detail terkait',
                ], 422);
            }

            $penerimaanFinance->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penerimaan finance berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus penerimaan finance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics for penerimaan finance.
     */
    public function getFinanceStats(): JsonResponse
    {
        try {
            $totalPenerimaan = PenerimaanFinance::sum('jumlah');
            $totalTransaksi = PenerimaanFinance::count();
            $penerimaanBulanIni = PenerimaanFinance::whereMonth('tgl_penerimaan', now()->month)
                ->whereYear('tgl_penerimaan', now()->year)
                ->sum('jumlah');

            $statusBreakdown = PenerimaanFinance::selectRaw('status, COUNT(*) as count, SUM(jumlah) as total')
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status => [
                        'count' => $item->count,
                        'total' => $item->total,
                    ]];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_penerimaan' => $totalPenerimaan,
                    'total_transaksi' => $totalTransaksi,
                    'penerimaan_bulan_ini' => $penerimaanBulanIni,
                    'status_breakdown' => $statusBreakdown,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik penerimaan finance',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
