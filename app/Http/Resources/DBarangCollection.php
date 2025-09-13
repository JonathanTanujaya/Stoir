<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DBarangCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_items' => $this->collection->count(),
                'stock_summary' => $this->getStockSummary(),
                'value_summary' => $this->getValueSummary(),
                'modal_distribution' => $this->getModalDistribution(),
                'product_distribution' => $this->getProductDistribution(),
            ],
        ];
    }

    /**
     * Get stock summary
     */
    private function getStockSummary(): array
    {
        $totalStock = $this->collection->sum('stok');
        $availableItems = $this->collection->where('stok', '>', 0)->count();
        $emptyItems = $this->collection->where('stok', '=', 0)->count();
        $lowStockItems = $this->collection->whereBetween('stok', [1, 4])->count();

        return [
            'total_stock' => $totalStock,
            'available_items' => $availableItems,
            'empty_items' => $emptyItems,
            'low_stock_items' => $lowStockItems,
            'average_stock' => $this->collection->count() > 0 ?
                round($totalStock / $this->collection->count(), 2) : 0,
            'max_stock' => $this->collection->max('stok') ?? 0,
            'min_stock' => $this->collection->min('stok') ?? 0,
        ];
    }

    /**
     * Get value summary
     */
    private function getValueSummary(): array
    {
        $totalValue = $this->collection->sum(function ($item) {
            return ($item->modal ?? 0) * ($item->stok ?? 0);
        });

        $averageModal = $this->collection->where('modal', '>', 0)->avg('modal') ?? 0;
        $maxModal = $this->collection->max('modal') ?? 0;
        $minModal = $this->collection->where('modal', '>', 0)->min('modal') ?? 0;

        return [
            'total_inventory_value' => number_format($totalValue, 2, '.', ''),
            'total_inventory_value_formatted' => 'Rp '.number_format($totalValue, 2, ',', '.'),
            'average_modal' => number_format($averageModal, 2, '.', ''),
            'average_modal_formatted' => 'Rp '.number_format($averageModal, 2, ',', '.'),
            'highest_modal' => number_format($maxModal, 2, '.', ''),
            'highest_modal_formatted' => 'Rp '.number_format($maxModal, 2, ',', '.'),
            'lowest_modal' => number_format($minModal, 2, '.', ''),
            'lowest_modal_formatted' => 'Rp '.number_format($minModal, 2, ',', '.'),
        ];
    }

    /**
     * Get modal price distribution
     */
    private function getModalDistribution(): array
    {
        $lowPrice = $this->collection->where('modal', '<=', 100000)->count();
        $midPrice = $this->collection->whereBetween('modal', [100001, 1000000])->count();
        $highPrice = $this->collection->where('modal', '>', 1000000)->count();
        $noPrice = $this->collection->where('modal', '<=', 0)->count();

        return [
            'low_price_items' => $lowPrice,     // <= 100k
            'mid_price_items' => $midPrice,     // 100k - 1M
            'high_price_items' => $highPrice,   // > 1M
            'no_price_items' => $noPrice,       // 0 or null
            'total_items' => $this->collection->count(),
        ];
    }

    /**
     * Get product distribution
     */
    private function getProductDistribution(): array
    {
        $productCounts = $this->collection->groupBy('kode_barang')->map->count();
        $productStocks = $this->collection->groupBy('kode_barang')->map(function ($items) {
            return $items->sum('stok');
        });

        return [
            'unique_products' => $productCounts->count(),
            'items_per_product' => $productCounts->map(function ($count) {
                return $count;
            })->toArray(),
            'stock_per_product' => $productStocks->map(function ($stock) {
                return $stock;
            })->toArray(),
            'average_items_per_product' => $productCounts->count() > 0 ?
                round($this->collection->count() / $productCounts->count(), 2) : 0,
        ];
    }
}
