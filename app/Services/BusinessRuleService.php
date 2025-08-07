<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\MCust;
use App\Models\DBarang;
use App\Models\KartuStok;
use App\Models\Journal;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessRuleService
{
    /**
     * Validate invoice business rules
     */
    public function validateInvoice(array $data): array
    {
        $errors = [];

        // Check customer credit limit
        if (isset($data['tipe']) && $data['tipe'] === 'CREDIT') {
            $creditError = $this->validateCustomerCredit($data['kodecust'], $data['total']);
            if ($creditError) {
                $errors['credit_limit'] = $creditError;
            }
        }

        // Check stock availability
        if (isset($data['details'])) {
            $stockErrors = $this->validateStockAvailability($data['details']);
            if (!empty($stockErrors)) {
                $errors['stock'] = $stockErrors;
            }
        }

        // Validate pricing rules
        if (isset($data['details'])) {
            $pricingErrors = $this->validatePricingRules($data['details'], $data['kodecust']);
            if (!empty($pricingErrors)) {
                $errors['pricing'] = $pricingErrors;
            }
        }

        // Check maximum transaction amount
        $maxAmountError = $this->validateMaxTransactionAmount($data['total']);
        if ($maxAmountError) {
            $errors['max_amount'] = $maxAmountError;
        }

        return $errors;
    }

    /**
     * Validate customer credit limit
     */
    public function validateCustomerCredit(string $kodecust, float $amount): ?string
    {
        $customer = MCust::where('kodecust', $kodecust)->first();
        
        if (!$customer) {
            return 'Customer not found';
        }

        if ($customer->credit_limit <= 0) {
            return null; // No credit limit set
        }

        // Calculate current outstanding debt
        $currentDebt = Invoice::where('kodecust', $kodecust)
            ->where('tipe', 'CREDIT')
            ->where('status', '!=', 'Cancelled')
            ->sum('saldo');

        $totalDebt = $currentDebt + $amount;

        if ($totalDebt > $customer->credit_limit) {
            return "Credit limit exceeded. Limit: {$customer->credit_limit}, Current debt: {$currentDebt}, Proposed: {$amount}";
        }

        return null;
    }

    /**
     * Validate stock availability
     */
    public function validateStockAvailability(array $items): array
    {
        $errors = [];

        foreach ($items as $index => $item) {
            $barang = DBarang::where('kodebarang', $item['kodebarang'])->first();
            
            if (!$barang) {
                $errors[$index] = "Item {$item['kodebarang']} not found";
                continue;
            }

            $availableStock = $barang->stok ?? 0;
            $requestedQty = $item['qty'] ?? 0;

            if ($requestedQty > $availableStock) {
                $errors[$index] = "Insufficient stock for {$item['kodebarang']}. Available: {$availableStock}, Requested: {$requestedQty}";
            }

            // Check if item is active
            if (isset($barang->status) && $barang->status !== 'Active') {
                $errors[$index] = "Item {$item['kodebarang']} is not active";
            }
        }

        return $errors;
    }

    /**
     * Validate pricing rules
     */
    public function validatePricingRules(array $items, string $kodecust): array
    {
        $errors = [];
        $customer = MCust::where('kodecust', $kodecust)->first();

        foreach ($items as $index => $item) {
            $barang = DBarang::where('kodebarang', $item['kodebarang'])->first();
            
            if (!$barang) {
                continue;
            }

            $proposedPrice = $item['harga'] ?? 0;
            $minPrice = $barang->harga_minimum ?? 0;
            $standardPrice = $barang->harga_jual ?? 0;

            // Check minimum price
            if ($proposedPrice < $minPrice) {
                $errors[$index] = "Price below minimum for {$item['kodebarang']}. Minimum: {$minPrice}, Proposed: {$proposedPrice}";
                continue;
            }

            // Check customer-specific pricing
            if ($customer && isset($customer->tipe_harga)) {
                $expectedPrice = $this->getCustomerPrice($barang, $customer->tipe_harga);
                $tolerance = $expectedPrice * 0.05; // 5% tolerance

                if (abs($proposedPrice - $expectedPrice) > $tolerance) {
                    $errors[$index] = "Price deviation for {$item['kodebarang']}. Expected: {$expectedPrice}, Proposed: {$proposedPrice}";
                }
            }
        }

        return $errors;
    }

    /**
     * Get customer-specific price
     */
    protected function getCustomerPrice(DBarang $barang, string $tipeHarga): float
    {
        switch ($tipeHarga) {
            case 'GROSIR':
                return $barang->harga_grosir ?? $barang->harga_jual ?? 0;
            case 'ECERAN':
                return $barang->harga_eceran ?? $barang->harga_jual ?? 0;
            case 'DISTRIBUTOR':
                return $barang->harga_distributor ?? $barang->harga_jual ?? 0;
            default:
                return $barang->harga_jual ?? 0;
        }
    }

    /**
     * Validate maximum transaction amount
     */
    public function validateMaxTransactionAmount(float $amount): ?string
    {
        $maxAmount = config('app.max_transaction_amount', 10000000); // 10M default
        
        if ($amount > $maxAmount) {
            return "Transaction amount exceeds maximum limit of {$maxAmount}";
        }

        return null;
    }

    /**
     * Validate stock transaction business rules
     */
    public function validateStockTransaction(array $data): array
    {
        $errors = [];

        // Validate transaction timing
        $timingError = $this->validateTransactionTiming($data['tanggal']);
        if ($timingError) {
            $errors['timing'] = $timingError;
        }

        // Validate stock movements
        if (isset($data['items'])) {
            $movementErrors = $this->validateStockMovements($data['items'], $data['jenis']);
            if (!empty($movementErrors)) {
                $errors['movements'] = $movementErrors;
            }
        }

        // Validate transfer rules
        if ($data['jenis'] === 'TRANSFER') {
            $transferError = $this->validateTransferRules($data);
            if ($transferError) {
                $errors['transfer'] = $transferError;
            }
        }

        return $errors;
    }

    /**
     * Validate transaction timing
     */
    public function validateTransactionTiming(string $tanggal): ?string
    {
        $transactionDate = Carbon::parse($tanggal);
        $now = Carbon::now();
        
        // Check if transaction is in the future
        if ($transactionDate->isFuture()) {
            return 'Transaction date cannot be in the future';
        }

        // Check if transaction is too old (configurable)
        $maxDaysBack = config('app.max_transaction_days_back', 365);
        if ($transactionDate->diffInDays($now) > $maxDaysBack) {
            return "Transaction date is more than {$maxDaysBack} days old";
        }

        // Check if period is closed
        if ($this->isPeriodClosed($transactionDate)) {
            return 'Cannot create transactions in closed period';
        }

        return null;
    }

    /**
     * Check if accounting period is closed
     */
    protected function isPeriodClosed(Carbon $date): bool
    {
        // Implement your period closing logic here
        // This could check against a periods table or configuration
        return false; // Placeholder
    }

    /**
     * Validate stock movements
     */
    public function validateStockMovements(array $items, string $jenis): array
    {
        $errors = [];

        foreach ($items as $index => $item) {
            $kodebarang = $item['kodebarang'];
            $qtyMasuk = $item['qtymasuk'] ?? 0;
            $qtyKeluar = $item['qtykeluar'] ?? 0;

            // Check stock for outgoing transactions
            if ($qtyKeluar > 0 && in_array($jenis, ['OUT', 'TRANSFER'])) {
                $currentStock = $this->getCurrentStock($kodebarang);
                
                if ($qtyKeluar > $currentStock) {
                    $errors[$index] = "Insufficient stock for {$kodebarang}. Current: {$currentStock}, Requested: {$qtyKeluar}";
                }
            }

            // Validate movement logic
            if ($jenis === 'IN' && $qtyMasuk <= 0) {
                $errors[$index] = "Incoming transaction must have positive quantity in";
            }

            if (in_array($jenis, ['OUT', 'TRANSFER']) && $qtyKeluar <= 0) {
                $errors[$index] = "Outgoing transaction must have positive quantity out";
            }
        }

        return $errors;
    }

    /**
     * Get current stock for an item
     */
    protected function getCurrentStock(string $kodebarang): float
    {
        $barang = DBarang::where('kodebarang', $kodebarang)->first();
        return $barang ? ($barang->stok ?? 0) : 0;
    }

    /**
     * Validate transfer rules
     */
    public function validateTransferRules(array $data): ?string
    {
        if (!isset($data['kodedivisi_tujuan'])) {
            return 'Destination division is required for transfers';
        }

        if ($data['kodedivisi'] === $data['kodedivisi_tujuan']) {
            return 'Source and destination divisions cannot be the same';
        }

        // Check if divisions are in the same company
        $sourceDivision = DB::table('dbo.m_divisi')->where('kodedivisi', $data['kodedivisi'])->first();
        $destDivision = DB::table('dbo.m_divisi')->where('kodedivisi', $data['kodedivisi_tujuan'])->first();

        if (!$sourceDivision || !$destDivision) {
            return 'Invalid division codes';
        }

        if (isset($sourceDivision->kodecompany) && isset($destDivision->kodecompany)) {
            if ($sourceDivision->kodecompany !== $destDivision->kodecompany) {
                return 'Inter-company transfers require special approval';
            }
        }

        return null;
    }

    /**
     * Validate journal entry business rules
     */
    public function validateJournalEntry(array $data): array
    {
        $errors = [];

        // Check if journal balances
        $balanceError = $this->validateJournalBalance($data['details'] ?? []);
        if ($balanceError) {
            $errors['balance'] = $balanceError;
        }

        // Validate COA codes
        $coaErrors = $this->validateCOACodes($data['details'] ?? []);
        if (!empty($coaErrors)) {
            $errors['coa'] = $coaErrors;
        }

        // Check duplicate entries
        $duplicateError = $this->checkDuplicateJournal($data);
        if ($duplicateError) {
            $errors['duplicate'] = $duplicateError;
        }

        return $errors;
    }

    /**
     * Validate journal balance (debit = credit)
     */
    protected function validateJournalBalance(array $details): ?string
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($details as $detail) {
            $totalDebit += $detail['debit'] ?? 0;
            $totalCredit += $detail['kredit'] ?? 0;
        }

        $difference = abs($totalDebit - $totalCredit);
        if ($difference > 0.01) { // Allow 1 cent tolerance
            return "Journal entry is not balanced. Debit: {$totalDebit}, Credit: {$totalCredit}";
        }

        return null;
    }

    /**
     * Validate Chart of Accounts codes
     */
    protected function validateCOACodes(array $details): array
    {
        $errors = [];

        foreach ($details as $index => $detail) {
            if (!isset($detail['kodecoa'])) {
                $errors[$index] = 'COA code is required';
                continue;
            }

            $coa = DB::table('dbo.coa')->where('kodecoa', $detail['kodecoa'])->first();
            if (!$coa) {
                $errors[$index] = "COA code {$detail['kodecoa']} not found";
                continue;
            }

            // Check if COA is active
            if (isset($coa->status) && $coa->status !== 'Active') {
                $errors[$index] = "COA code {$detail['kodecoa']} is not active";
            }

            // Check if it's a detail account (can accept transactions)
            if (isset($coa->is_detail) && !$coa->is_detail) {
                $errors[$index] = "COA code {$detail['kodecoa']} is a header account and cannot accept transactions";
            }
        }

        return $errors;
    }

    /**
     * Check for duplicate journal entries
     */
    protected function checkDuplicateJournal(array $data): ?string
    {
        if (!isset($data['noreferensi']) || !isset($data['tanggal'])) {
            return null;
        }

        $existing = Journal::where('noreferensi', $data['noreferensi'])
            ->where('tanggal', $data['tanggal'])
            ->where('kodedivisi', $data['kodedivisi'] ?? '')
            ->first();

        if ($existing) {
            return 'Duplicate journal entry detected';
        }

        return null;
    }
}
