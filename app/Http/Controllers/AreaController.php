<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Http\Resources\AreaCollection;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of areas.
     */
    public function index(Request $request): JsonResponse
    {
        $request->attributes->set('query_start_time', microtime(true));

        $query = Area::query();

        // Eager load counts for summary when listing
        $with = $request->boolean('with_relations', false);
        if ($with) {
            $query->with(['customers', 'sales']);
        }

        // Search (ILIKE for Postgres case-insensitive)
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode_area', 'ILIKE', "%{$search}%")
                  ->orWhere('area', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (! is_null($status)) {
                $query->where('status', $status);
            }
        }

        // Sorting
        $sort = $request->get('sort', $request->get('sort_by', 'kode_area'));
        $direction = $request->get('direction', $request->get('sort_order', 'asc'));
        $allowedSorts = ['kode_area', 'area', 'status'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 15);
        $perPage = max(1, min(100, $perPage));
        $paginator = $query->paginate($perPage)->withQueryString();

        return response()->json(new AreaCollection($paginator));
    }

    /**
     * Store a newly created area in storage.
     */
    public function store(StoreAreaRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $area = Area::create($validated);
        $area->load(['customers', 'sales']);

        return response()->json([
            'success' => true,
            'message' => 'Area berhasil dibuat.',
            'data' => new AreaResource($area),
        ], 201);
    }

    /**
     * Display the specified area.
     */
    public function show(string $kodeArea): JsonResponse
    {
        $area = Area::with(['customers', 'sales'])
            ->where('kode_area', $kodeArea)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new AreaResource($area),
        ]);
    }

    /**
     * Update the specified area in storage.
     */
    public function update(UpdateAreaRequest $request, string $kodeArea): JsonResponse
    {
        $area = Area::where('kode_area', $kodeArea)
            ->firstOrFail();

        $validated = $request->validated();
        $area->update($validated);

        $area->load(['customers', 'sales']);

        return response()->json([
            'success' => true,
            'message' => 'Area berhasil diperbarui.',
            'data' => new AreaResource($area),
        ]);
    }

    /**
     * Remove the specified area from storage.
     */
    public function destroy(string $kodeArea): JsonResponse
    {
        $area = Area::where('kode_area', $kodeArea)
            ->firstOrFail();

        // Check if area has related customers or sales
        $customersCount = $area->customers()->count();
        $salesCount = $area->sales()->count();

        if ($customersCount > 0 || $salesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Area tidak dapat dihapus karena masih memiliki data terkait.',
                'details' => [
                    'customers_count' => $customersCount,
                    'sales_count' => $salesCount,
                ],
            ], 422);
        }

        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Area berhasil dihapus.',
        ]);
    }

    /**
     * Get area statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_areas' => Area::count(),
            'active_areas' => Area::where('status', true)->count(),
            'inactive_areas' => Area::where('status', false)->count(),
        ];

        $areas = Area::with(['customers', 'sales'])
            ->get();

        $stats['total_customers'] = $areas->sum(function ($area) {
            return $area->customers->count();
        });

        $stats['total_sales'] = $areas->sum(function ($area) {
            return $area->sales->count();
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
