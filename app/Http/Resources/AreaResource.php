<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'kode_area' => $this->kode_area,
            'area' => $this->area,
            'status' => (bool) $this->status,
            'status_label' => $this->status ? 'Aktif' : 'Tidak Aktif',
            'status_badge_class' => $this->status ? 'success' : 'danger',
            
            // Stats - check if relationships are loaded
            'customers_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('customers'),
                function () {
                    return $this->customers->count();
                }
            ),
            'active_customers_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('customers'),
                function () {
                    return $this->customers->where('status', true)->count();
                }
            ),
            'sales_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('sales'),
                function () {
                    return $this->sales->count();
                }
            ),
            'active_sales_count' => $this->when(
                method_exists($this->resource, 'relationLoaded') && $this->relationLoaded('sales'),
                function () {
                    return $this->sales->where('status', true)->count();
                }
            ),
            
            // Additional data for UI
            'display_name' => "[{$this->kode_area}] {$this->area}",
        ];
    }
}
