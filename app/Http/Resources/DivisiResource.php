<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DivisiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'kode_divisi' => $this->kode_divisi,
            'nama_divisi' => $this->nama_divisi,
            
            // Relationships with counts
            'banks_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('banks'),
                function () {
                    return $this->banks->count();
                }
            ),
            'areas_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('areas'),
                function () {
                    return $this->areas->count();
                }
            ),
            'customers_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('customers'),
                function () {
                    return $this->customers->count();
                }
            ),
            'sales_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('sales'),
                function () {
                    return $this->sales->count();
                }
            ),
            'barangs_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('barangs'),
                function () {
                    return $this->barangs->count();
                }
            ),
            'invoices_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('invoices'),
                function () {
                    return $this->invoices->count();
                }
            ),
            'suppliers_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('suppliers'),
                function () {
                    return $this->suppliers->count();
                }
            ),
            
            // Relationship data when loaded
            'banks' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('banks'),
                function () {
                    return $this->banks->map(function ($bank) {
                        return [
                            'kode_bank' => $bank->kode_bank,
                            'bank' => $bank->bank,
                            'status' => (bool) $bank->status,
                        ];
                    });
                }
            ),
            'areas' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('areas'),
                function () {
                    return $this->areas->map(function ($area) {
                        return [
                            'kode_area' => $area->kode_area,
                            'area' => $area->area,
                            'status' => (bool) $area->status,
                        ];
                    });
                }
            ),
            
            // Additional data for UI
            'display_name' => "[{$this->kode_divisi}] {$this->nama_divisi}",
            'has_data' => $this->when(
                method_exists($this->resource, 'relationLoaded'),
                function () {
                    $hasBanks = $this->relationLoaded('banks') && $this->banks->count() > 0;
                    $hasAreas = $this->relationLoaded('areas') && $this->areas->count() > 0;
                    $hasCustomers = $this->relationLoaded('customers') && $this->customers->count() > 0;
                    $hasSales = $this->relationLoaded('sales') && $this->sales->count() > 0;
                    $hasBarangs = $this->relationLoaded('barangs') && $this->barangs->count() > 0;
                    $hasInvoices = $this->relationLoaded('invoices') && $this->invoices->count() > 0;
                    $hasSuppliers = $this->relationLoaded('suppliers') && $this->suppliers->count() > 0;
                    
                    return $hasBanks || $hasAreas || $hasCustomers || $hasSales || $hasBarangs || $hasInvoices || $hasSuppliers;
                }
            ),
        ];
    }
}
