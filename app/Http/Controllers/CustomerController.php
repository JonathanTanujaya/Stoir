<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $request->attributes->set('query_start_time', microtime(true));

            $query = Customer::query()
                ->with(['area', 'sales']);

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('kode_cust', 'ILIKE', "%{$search}%")
                      ->orWhere('nama_cust', 'ILIKE', "%{$search}%")
                      ->orWhere('alamat', 'ILIKE', "%{$search}%")
                      ->orWhere('telp', 'ILIKE', "%{$search}%")
                      ->orWhere('contact', 'ILIKE', "%{$search}%");
                });
            }

            if ($request->filled('area')) {
                $query->where('kode_area', strtoupper($request->get('area')));
            }
            if ($request->filled('sales')) {
                $query->where('kode_sales', strtoupper($request->get('sales')));
            }
            if ($request->filled('status')) {
                $query->where('status', $request->boolean('status'));
            }
            if ($request->filled('min_credit')) {
                $query->where('credit_limit', '>=', (float) $request->get('min_credit'));
            }
            if ($request->filled('max_credit')) {
                $query->where('credit_limit', '<=', (float) $request->get('max_credit'));
            }

            $sort = $request->get('sort', $request->get('sort_by', 'kode_cust'));
            $direction = $request->get('direction', $request->get('sort_order', 'asc'));
            $allowedSort = ['kode_cust', 'nama_cust', 'kode_area', 'kode_sales', 'status', 'credit_limit'];
            if (in_array($sort, $allowedSort, true)) {
                $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
            }

            $perPage = min((int) $request->get('per_page', 15), 100);
            $customers = $query->paginate($perPage);

            return response()->json(new CustomerCollection($customers));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            $validated = $request->validated();

            $customer = Customer::create($validated);
            $customer->load(['area', 'sales']);

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dibuat',
                'data' => (new CustomerResource($customer))->resolve($request),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kodeCust)
    {
        try {
            $customer = Customer::where('kode_cust', $kodeCust)
                                ->with(['area', 'sales'])
                                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => (new CustomerResource($customer))->resolve(request()),
            ]);
        } catch (\Throwable $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => $status === 404 ? 'Customer tidak ditemukan' : 'Gagal mengambil data customer',
                'error' => $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, string $kodeCust)
    {
        try {
            $customer = Customer::where('kode_cust', $kodeCust)
                                ->firstOrFail();

            $validated = $request->validated();
            $customer->update($validated);
            $customer->load(['area', 'sales']);

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil diperbarui',
                'data' => (new CustomerResource($customer))->resolve($request),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kodeCust)
    {
        try {
            $customer = Customer::where('kode_cust', $kodeCust)
                                ->firstOrFail();

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dihapus'
            ], 200);
        } catch (\Throwable $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => $status === 404 ? 'Customer tidak ditemukan' : 'Gagal menghapus customer',
                'error' => $e->getMessage(),
            ], $status);
        }
    }
}