<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanFinanceDetail;
use App\Models\PenerimaanFinance;
use App\Http\Requests\StorePenerimaanFinanceDetailRequest;
use App\Http\Requests\UpdatePenerimaanFinanceDetailRequest;
use App\Http\Resources\PenerimaanFinanceDetailResource;
use App\Http\Resources\PenerimaanFinanceDetailCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenerimaanFinanceDetailController extends Controller
{
    /**
     * Display a listing of the penerimaan finance details.
     */
    public function index(Request $request, string $noPenerimaan): JsonResponse
    {
        try {
            // Verify parent penerimaan finance exists
            $penerimaanFinance = PenerimaanFinance::where('no_penerimaan', $noPenerimaan)
                ->firstOrFail();

            $query = PenerimaanFinanceDetail::query()
                ->where('no_penerimaan', $noPenerimaan)
                ->with(['penerimaanFinance', 'invoice']);

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('no_invoice', 'ILIKE', "%{$search}%")
                      ->orWhere('status', 'ILIKE', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('no_invoice')) {
                $query->where('no_invoice', $request->input('no_invoice'));
            }

            if ($request->filled('min_jumlah_invoice')) {
                $query->where('jumlah_invoice', '>=', $request->input('min_jumlah_invoice'));
            }

            if ($request->filled('max_jumlah_invoice')) {
                $query->where('jumlah_invoice', '<=', $request->input('max_jumlah_invoice'));
            }

            if ($request->filled('payment_status')) {
                $paymentStatus = $request->input('payment_status');
                switch ($paymentStatus) {
                    case 'fully_paid':
                        $query->whereRaw('(jumlah_bayar + jumlah_dispensasi) >= jumlah_invoice');
                        break;
                    case 'partially_paid':
                        $query->whereRaw('(jumlah_bayar + jumlah_dispensasi) > 0 AND (jumlah_bayar + jumlah_dispensasi) < jumlah_invoice');
                        break;
                    case 'unpaid':
                        $query->whereRaw('(jumlah_bayar + jumlah_dispensasi) = 0');
                        break;
                }
            }

            // Apply sorting
            $sortBy = $request->input('sort_by', 'id');
            $sortDirection = $request->input('sort_direction', 'asc');
            
            $allowedSortFields = ['id', 'no_invoice', 'jumlah_invoice', 'sisa_invoice', 'jumlah_bayar', 'jumlah_dispensasi', 'status'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortDirection);
            }

            // Paginate results
            $perPage = min($request->input('per_page', 15), 100);
            $details = $query->paginate($perPage);

            return response()->json(new PenerimaanFinanceDetailCollection($details));

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Penerimaan finance tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created penerimaan finance detail.
     */
    public function store(StorePenerimaanFinanceDetailRequest $request, string $noPenerimaan): JsonResponse
    {
        try {
            // Verify parent penerimaan finance exists
            $penerimaanFinance = PenerimaanFinance::where('no_penerimaan', $noPenerimaan)
                ->firstOrFail();

            // Check for duplicate invoice in the same penerimaan
            $existingDetail = PenerimaanFinanceDetail::where('no_penerimaan', $noPenerimaan)
                ->where('no_invoice', $request->no_invoice)
                ->first();

            if ($existingDetail) {
                return response()->json([
                    'message' => 'Invoice sudah ada dalam penerimaan finance ini.',
                    'errors' => [
                        'no_invoice' => ['Invoice sudah ada dalam penerimaan finance ini.']
                    ]
                ], 409);
            }

            $detail = PenerimaanFinanceDetail::create($request->validated());

            return response()->json([
                'message' => 'Detail penerimaan finance berhasil ditambahkan',
                'data' => new PenerimaanFinanceDetailResource($detail->load(['penerimaanFinance', 'invoice']))
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Penerimaan finance tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified penerimaan finance detail.
     */
    public function show(string $noPenerimaan, PenerimaanFinanceDetail $detail): JsonResponse
    {
        try {
            // Verify the detail belongs to the specified penerimaan finance
            if ($detail->no_penerimaan !== $noPenerimaan) {
                return response()->json([
                    'message' => 'Detail tidak ditemukan atau tidak terkait dengan penerimaan finance ini.'
                ], 404);
            }

            $detail->load(['penerimaanFinance', 'invoice']);

            return response()->json([
                'data' => new PenerimaanFinanceDetailResource($detail)
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detail penerimaan finance tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified penerimaan finance detail.
     */
    public function update(UpdatePenerimaanFinanceDetailRequest $request, string $noPenerimaan, PenerimaanFinanceDetail $detail): JsonResponse
    {
        try {
            // Verify the detail belongs to the specified penerimaan finance
            if ($detail->no_penerimaan !== $noPenerimaan) {
                return response()->json([
                    'message' => 'Detail tidak ditemukan atau tidak terkait dengan penerimaan finance ini.'
                ], 404);
            }

            // Check for duplicate invoice if invoice is being changed
            if ($request->filled('no_invoice') && $request->no_invoice !== $detail->no_invoice) {
                $existingDetail = PenerimaanFinanceDetail::where('no_penerimaan', $noPenerimaan)
                    ->where('no_invoice', $request->no_invoice)
                    ->where('id', '!=', $detail->id)
                    ->first();

                if ($existingDetail) {
                    return response()->json([
                        'message' => 'Invoice sudah ada dalam penerimaan finance ini.',
                        'errors' => [
                            'no_invoice' => ['Invoice sudah ada dalam penerimaan finance ini.']
                        ]
                    ], 409);
                }
            }

            $detail->update($request->validated());
            $detail->load(['penerimaanFinance', 'invoice']);

            return response()->json([
                'message' => 'Detail penerimaan finance berhasil diperbarui',
                'data' => new PenerimaanFinanceDetailResource($detail)
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detail penerimaan finance tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified penerimaan finance detail.
     */
    public function destroy(string $noPenerimaan, PenerimaanFinanceDetail $detail): JsonResponse
    {
        try {
            // Verify the detail belongs to the specified penerimaan finance
            if ($detail->no_penerimaan !== $noPenerimaan) {
                return response()->json([
                    'message' => 'Detail tidak ditemukan atau tidak terkait dengan penerimaan finance ini.'
                ], 404);
            }

            $detail->delete();

            return response()->json([
                'message' => 'Detail penerimaan finance berhasil dihapus'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Detail penerimaan finance tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for penerimaan finance details.
     */
    public function stats(Request $request, string $noPenerimaan): JsonResponse
    {
        try {
            // Verify parent penerimaan finance exists
            $penerimaanFinance = PenerimaanFinance::where('no_penerimaan', $noPenerimaan)
                ->firstOrFail();

            $query = PenerimaanFinanceDetail::query()
                ->where('no_penerimaan', $noPenerimaan);

            // Apply filters if provided
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('no_invoice')) {
                $query->where('no_invoice', $request->input('no_invoice'));
            }

            $details = $query->get();

            $stats = [
                'total_items' => $details->count(),
                'total_jumlah_invoice' => $details->sum('jumlah_invoice'),
                'total_jumlah_bayar' => $details->sum('jumlah_bayar'),
                'total_jumlah_dispensasi' => $details->sum('jumlah_dispensasi'),
                'total_pembayaran' => $details->sum(fn($item) => $item->jumlah_bayar + $item->jumlah_dispensasi),
                'total_sisa_tagihan' => $details->sum(fn($item) => $item->jumlah_invoice - ($item->jumlah_bayar + $item->jumlah_dispensasi)),
                'average_jumlah_invoice' => round($details->avg('jumlah_invoice') ?? 0, 2),
                'average_jumlah_bayar' => round($details->avg('jumlah_bayar') ?? 0, 2),
                'average_payment_percentage' => $details->count() > 0 
                    ? round($details->map(function ($item) {
                        return $item->jumlah_invoice > 0 
                            ? (($item->jumlah_bayar + $item->jumlah_dispensasi) / $item->jumlah_invoice) * 100 
                            : 0;
                    })->avg(), 2)
                    : 0,
                'status_breakdown' => $details->groupBy('status')->map->count(),
                'payment_status_breakdown' => [
                    'fully_paid' => $details->filter(function ($item) {
                        return ($item->jumlah_bayar + $item->jumlah_dispensasi) >= $item->jumlah_invoice;
                    })->count(),
                    'partially_paid' => $details->filter(function ($item) {
                        $totalPaid = $item->jumlah_bayar + $item->jumlah_dispensasi;
                        return $totalPaid > 0 && $totalPaid < $item->jumlah_invoice;
                    })->count(),
                    'unpaid' => $details->filter(function ($item) {
                        return ($item->jumlah_bayar + $item->jumlah_dispensasi) == 0;
                    })->count(),
                ],
                'top_invoices_by_amount' => $details->sortByDesc('jumlah_invoice')
                    ->take(10)
                    ->values()
                    ->map(function ($item) {
                        return [
                            'no_invoice' => $item->no_invoice,
                            'jumlah_invoice' => $item->jumlah_invoice,
                            'total_pembayaran' => $item->jumlah_bayar + $item->jumlah_dispensasi,
                            'sisa_tagihan' => $item->jumlah_invoice - ($item->jumlah_bayar + $item->jumlah_dispensasi),
                        ];
                    }),
                'invoice_summary' => [
                    'unique_invoices' => $details->pluck('no_invoice')->unique()->count(),
                    'max_invoice_amount' => $details->max('jumlah_invoice') ?? 0,
                    'min_invoice_amount' => $details->min('jumlah_invoice') ?? 0,
                    'max_payment_amount' => $details->max('jumlah_bayar') ?? 0,
                    'min_payment_amount' => $details->min('jumlah_bayar') ?? 0,
                ],
            ];

            return response()->json(['stats' => $stats]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Penerimaan finance tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil statistik.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
