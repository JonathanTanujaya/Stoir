<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'no_invoice' => $this->no_invoice,
            'tgl_faktur' => $this->tgl_faktur?->format('Y-m-d'),
            'tgl_faktur_formatted' => $this->tgl_faktur?->format('d/m/Y'),
            'kode_cust' => $this->kode_cust,
            'kode_sales' => $this->kode_sales,
            'nama_divisi' => $this->whenLoaded('divisi', function () {
                return $this->divisi->nama_divisi ?? null;
            }),
            'nama_customer' => $this->whenLoaded('customer', function () {
                return $this->customer->nama_cust ?? null;
            }),
            'nama_sales' => $this->whenLoaded('sales', function () {
                return $this->sales->nama_sales ?? null;
            }),
            'invoice_info' => [
                'tipe' => $this->tipe,
                'tipe_text' => $this->getTipeText(),
                'jatuh_tempo' => $this->jatuh_tempo?->format('Y-m-d'),
                'jatuh_tempo_formatted' => $this->jatuh_tempo?->format('d/m/Y'),
                'days_until_due' => $this->getDaysUntilDue(),
                'is_overdue' => $this->isOverdue(),
                'overdue_days' => $this->getOverdueDays(),
            ],
            'financial' => [
                'total' => $this->formatCurrency($this->total),
                'total_raw' => (float) $this->total,
                'disc' => $this->formatCurrency($this->disc),
                'disc_raw' => (float) $this->disc,
                'pajak' => $this->formatCurrency($this->pajak),
                'pajak_raw' => (float) $this->pajak,
                'grand_total' => $this->formatCurrency($this->grand_total),
                'grand_total_raw' => (float) $this->grand_total,
                'sisa_invoice' => $this->formatCurrency($this->sisa_invoice),
                'sisa_invoice_raw' => (float) $this->sisa_invoice,
                'paid_amount' => $this->formatCurrency($this->grand_total - $this->sisa_invoice),
                'paid_amount_raw' => (float) ($this->grand_total - $this->sisa_invoice),
                'payment_percentage' => $this->getPaymentPercentage(),
            ],
            'status_info' => [
                'status' => $this->status,
                'status_badge_class' => $this->getStatusBadgeClass(),
                'can_be_paid' => $this->canBePaid(),
                'can_be_cancelled' => $this->canBeCancelled(),
                'can_be_edited' => $this->canBeEdited(),
            ],
            'additional_info' => [
                'ket' => $this->ket,
                'username' => $this->username,
                'tt' => $this->tt,
            ],
            'audit' => [
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
                'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
                'created_at_human' => $this->created_at?->diffForHumans(),
                'updated_at_human' => $this->updated_at?->diffForHumans(),
            ],
            'relationships' => [
                'divisi' => $this->whenLoaded('divisi'),
                'customer' => $this->whenLoaded('customer'),
                'sales' => $this->whenLoaded('sales'),
                'invoice_details_count' => $this->whenCounted('invoiceDetails'),
                'has_details' => $this->when($this->relationLoaded('invoiceDetails'), function () {
                    return $this->invoiceDetails->isNotEmpty();
                }),
            ]
        ];
    }

    /**
     * Get tipe text representation
     */
    private function getTipeText(): string
    {
        return match($this->tipe) {
            '1' => 'Cash',
            '2' => 'Credit',
            default => 'Unknown'
        };
    }

    /**
     * Get days until due date
     */
    private function getDaysUntilDue(): int
    {
        if (!$this->jatuh_tempo) {
            return 0;
        }
        
        return now()->diffInDays($this->jatuh_tempo, false);
    }

    /**
     * Check if invoice is overdue
     */
    private function isOverdue(): bool
    {
        if (!$this->jatuh_tempo || $this->status === 'Lunas') {
            return false;
        }
        
        return now()->isAfter($this->jatuh_tempo) && $this->sisa_invoice > 0;
    }

    /**
     * Get overdue days
     */
    private function getOverdueDays(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return $this->jatuh_tempo->diffInDays(now());
    }

    /**
     * Get payment percentage
     */
    private function getPaymentPercentage(): float
    {
        if ($this->grand_total <= 0) {
            return 0;
        }
        
        $paidAmount = $this->grand_total - $this->sisa_invoice;
        return round(($paidAmount / $this->grand_total) * 100, 2);
    }

    /**
     * Get status badge CSS class
     */
    private function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'Open' => 'badge-primary',
            'Lunas' => 'badge-success',
            'Belum Lunas' => 'badge-warning',
            'Partial' => 'badge-info',
            'Cancel' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Check if invoice can be paid
     */
    private function canBePaid(): bool
    {
        return in_array($this->status, ['Open', 'Belum Lunas', 'Partial']) && $this->sisa_invoice > 0;
    }

    /**
     * Check if invoice can be cancelled
     */
    private function canBeCancelled(): bool
    {
        return in_array($this->status, ['Open', 'Belum Lunas']) && $this->sisa_invoice == $this->grand_total;
    }

    /**
     * Check if invoice can be edited
     */
    private function canBeEdited(): bool
    {
        return in_array($this->status, ['Open', 'Belum Lunas']);
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
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'api_version' => '1.0',
            ]
        ];
    }
}
