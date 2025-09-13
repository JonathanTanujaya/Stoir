<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => InvoiceResource::collection($this->collection),
            'summary' => [
                'total_invoices' => $this->collection->count(),
                'total_value' => $this->formatCurrency($this->collection->sum('grand_total')),
                'total_value_raw' => (float) $this->collection->sum('grand_total'),
                'total_outstanding' => $this->formatCurrency($this->collection->sum('sisa_invoice')),
                'total_outstanding_raw' => (float) $this->collection->sum('sisa_invoice'),
                'total_paid' => $this->formatCurrency($this->collection->sum('grand_total') - $this->collection->sum('sisa_invoice')),
                'total_paid_raw' => (float) ($this->collection->sum('grand_total') - $this->collection->sum('sisa_invoice')),
                'average_invoice_value' => $this->formatCurrency($this->collection->avg('grand_total')),
                'average_invoice_value_raw' => (float) $this->collection->avg('grand_total'),
                'status_breakdown' => [
                    'open' => $this->collection->where('status', 'Open')->count(),
                    'lunas' => $this->collection->where('status', 'Lunas')->count(),
                    'belum_lunas' => $this->collection->where('status', 'Belum Lunas')->count(),
                    'partial' => $this->collection->where('status', 'Partial')->count(),
                    'cancel' => $this->collection->where('status', 'Cancel')->count(),
                ],
                'overdue_invoices' => $this->getOverdueCount(),
                'due_today' => $this->getDueTodayCount(),
                'due_this_week' => $this->getDueThisWeekCount(),
            ],
            'pagination' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'filters_applied' => [
                'search' => $request->get('search'),
                'customer' => $request->get('customer'),
                'sales' => $request->get('sales'),
                'status' => $request->get('status'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'overdue_only' => $request->boolean('overdue_only'),
                'sort' => $request->get('sort', 'tgl_faktur'),
                'direction' => $request->get('direction', 'desc'),
            ]
        ];
    }

    /**
     * Get count of overdue invoices
     */
    private function getOverdueCount(): int
    {
        return $this->collection->filter(function ($invoice) {
            return $invoice->jatuh_tempo && 
                   now()->isAfter($invoice->jatuh_tempo) && 
                   $invoice->sisa_invoice > 0 &&
                   $invoice->status !== 'Lunas';
        })->count();
    }

    /**
     * Get count of invoices due today
     */
    private function getDueTodayCount(): int
    {
        return $this->collection->filter(function ($invoice) {
            return $invoice->jatuh_tempo && 
                   $invoice->jatuh_tempo->isToday() && 
                   $invoice->sisa_invoice > 0 &&
                   $invoice->status !== 'Lunas';
        })->count();
    }

    /**
     * Get count of invoices due this week
     */
    private function getDueThisWeekCount(): int
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        return $this->collection->filter(function ($invoice) use ($startOfWeek, $endOfWeek) {
            return $invoice->jatuh_tempo && 
                   $invoice->jatuh_tempo->between($startOfWeek, $endOfWeek) && 
                   $invoice->sisa_invoice > 0 &&
                   $invoice->status !== 'Lunas';
        })->count();
    }

    /**
     * Format currency value
     */
    private function formatCurrency($value): string
    {
        if (is_null($value)) {
            return 'Rp 0';
        }
        
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }

    /**
     * Get additional data to be added to the response.
     */
    public function with(Request $request): array
    {
        $queryStartTime = $request->attributes->get('query_start_time', microtime(true));
        
        return [
            'api_version' => '1.0',
            'timestamp' => now()->toISOString(),
            'endpoint' => $request->url(),
            'query_time' => round((microtime(true) - $queryStartTime) * 1000, 2) . 'ms',
        ];
    }

    /**
     * Customize the pagination information for the resource.
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'pagination' => [
                'current_page' => $default['meta']['current_page'],
                'last_page' => $default['meta']['last_page'],
                'per_page' => $default['meta']['per_page'],
                'total' => $default['meta']['total'],
                'from' => $default['meta']['from'],
                'to' => $default['meta']['to'],
                'has_more_pages' => $default['meta']['current_page'] < $default['meta']['last_page'],
            ],
            'navigation' => [
                'first_page_url' => $default['links']['first'],
                'last_page_url' => $default['links']['last'],
                'prev_page_url' => $default['links']['prev'],
                'next_page_url' => $default['links']['next'],
            ]
        ];
    }
}
