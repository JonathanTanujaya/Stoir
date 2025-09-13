<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DBankCollection extends ResourceCollection
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
                'total_accounts' => $this->collection->count(),
                'saldo_summary' => $this->getSaldoSummary(),
                'status_breakdown' => $this->getStatusBreakdown(),
                'bank_distribution' => $this->getBankDistribution(),
            ],
        ];
    }

    /**
     * Get saldo summary
     */
    private function getSaldoSummary(): array
    {
        $totalSaldo = $this->collection->sum('saldo');
        $activeSaldo = $this->collection->where('saldo', '>', 0)->sum('saldo');
        $zeroSaldo = $this->collection->where('saldo', '=', 0)->count();
        $negativeSaldo = $this->collection->where('saldo', '<', 0)->sum('saldo');

        return [
            'total_saldo' => number_format($totalSaldo, 2, '.', ''),
            'total_saldo_formatted' => 'Rp '.number_format($totalSaldo, 2, ',', '.'),
            'active_saldo' => number_format($activeSaldo, 2, '.', ''),
            'active_saldo_formatted' => 'Rp '.number_format($activeSaldo, 2, ',', '.'),
            'zero_accounts' => $zeroSaldo,
            'negative_saldo' => number_format($negativeSaldo, 2, '.', ''),
            'negative_saldo_formatted' => 'Rp '.number_format($negativeSaldo, 2, ',', '.'),
            'average_saldo' => $this->collection->count() > 0 ?
                number_format($totalSaldo / $this->collection->count(), 2, '.', '') : '0.00',
        ];
    }

    /**
     * Get status breakdown
     */
    private function getStatusBreakdown(): array
    {
        $statusCounts = $this->collection->groupBy('status')->map->count();

        $saldoBasedStatus = [
            'active' => $this->collection->where('saldo', '>', 0)->count(),
            'zero' => $this->collection->where('saldo', '=', 0)->count(),
            'negative' => $this->collection->where('saldo', '<', 0)->count(),
        ];

        return [
            'by_status_field' => $statusCounts->toArray(),
            'by_saldo_value' => $saldoBasedStatus,
            'total_accounts' => $this->collection->count(),
        ];
    }

    /**
     * Get bank distribution
     */
    private function getBankDistribution(): array
    {
        $bankCounts = $this->collection->groupBy('kode_bank')->map->count();
        $bankSaldos = $this->collection->groupBy('kode_bank')->map(function ($accounts) {
            return $accounts->sum('saldo');
        });

        return [
            'account_count_by_bank' => $bankCounts->toArray(),
            'saldo_by_bank' => $bankSaldos->map(function ($saldo) {
                return number_format($saldo, 2, '.', '');
            })->toArray(),
            'total_banks' => $bankCounts->count(),
        ];
    }
}
