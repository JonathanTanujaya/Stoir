<?php
// scripts/table_route_mapping.php
// Updated table-to-route mapping using actual route data

// Collect actual route URIs from artisan output
$routeData = json_decode(file_get_contents('php://stdin'), true);
if (!$routeData) {
    // If no stdin, use artisan command
    $output = shell_exec('php artisan route:list --json');
    $routeData = json_decode($output, true);
}

$existingRoutes = [];
foreach ($routeData as $route) {
    $uri = strtolower(str_replace('api/', '', $route['uri']));
    $existingRoutes[$uri] = true;
}

// Source table list (from scipt.txt provided)
$tables = [
    'journal',
    'kartustok',
    'invoice_detail',
    'invoice',
    'penerimaanfinance_detail',
    'penerimaanfinance',
    'partpenerimaan_detail',
    'partpenerimaan',
    'd_barang',
    'saldobank',
    'm_resi',
    'm_cust',
    'returpenerimaan_detail',
    'returpenerimaan',
    'm_kategori',
    'user_module',
    'm_module',
    'm_supplier',
    'returnsales_detail',
    'returnsales',
    'm_dokumen',
    'm_area',
    'm_sales',
    'master_user',
    'd_bank',
    'tmpprintinvoice',
    'm_divisi',
    'm_bank',
    'spv',
    'company',
];

// Heuristic expected primary endpoint(s) per table.
// Each value: array of candidate route URI stems (without leading /api if using artisan serve) to test.
$expectedMapping = [
    'journal' => ['journals', 'journals/all'],
    'kartustok' => ['kartu-stok', 'kartu-stok/all', 'kartu-stok/summary'],
    'invoice_detail' => ['invoice-details', 'invoices/{id}/details'],
    'invoice' => ['invoices'],
    'penerimaanfinance_detail' => ['penerimaan-finance', 'penerimaan-finance/all'], // details embedded
    'penerimaanfinance' => ['penerimaan-finance', 'penerimaan-finance/all'],
    'partpenerimaan_detail' => ['part-penerimaan', 'part-penerimaan/all'],
    'partpenerimaan' => ['part-penerimaan', 'part-penerimaan/all'],
    'd_barang' => ['barang', 'barangs', 'spareparts'],
    'saldobank' => ['saldo-bank'],
    'm_resi' => ['m-resi','resi','resis'], // controller imported but maybe route missing
    'm_cust' => ['customers'],
    'returpenerimaan_detail' => ['return-purchases','return-purchases/purchase-details/{id}'],
    'returpenerimaan' => ['return-purchases'],
    'm_kategori' => ['kategoris','categories'],
    'user_module' => ['user-modules','modules'],
    'm_module' => ['user-modules','modules'],
    'm_supplier' => ['suppliers','master-suppliers'],
    'returnsales_detail' => ['return-sales','v-return-sales-detail'],
    'returnsales' => ['return-sales'],
    'm_dokumen' => ['dokumens','documents'],
    'm_area' => ['areas'],
    'm_sales' => ['sales'],
    'master_user' => ['master-users','users'],
    'd_bank' => ['banks'],
    'tmpprintinvoice' => ['tmp-print-invoices'],
    'm_divisi' => ['divisis','divisions'],
    'm_bank' => ['banks'],
    'spv' => ['spv','spvs'],
    'company' => ['companies'],
];

// Helper to expand pattern with placeholders to a check set.
function expandCandidates(array $candidates): array {
    $expanded = [];
    foreach ($candidates as $c) {
        // Remove placeholders for existence test (we only care about base prefix).
        $expanded[] = strtolower(preg_replace('/\{[^}]+\}/', '*', $c));
    }
    return array_unique($expanded);
}

// Function to test if any candidate (with wildcard *) matches an existing route.
function anyRouteExists(array $candidates, array $existing): array {
    $matched = [];
    foreach ($candidates as $pattern) {
        $regex = '#^' . str_replace('\*', '[^/]+', preg_quote($pattern, '#')) . '$#';
        foreach ($existing as $uri => $_) {
            if (preg_match($regex, $uri)) {
                $matched[] = $uri;
            }
        }
    }
    return array_unique($matched);
}

$rows = [];
foreach ($tables as $table) {
    $candidates = $expectedMapping[$table] ?? [];
    $expanded = expandCandidates($candidates);
    $matched = anyRouteExists($expanded, $existingRoutes);
    $status = empty($candidates) ? 'NO_MAPPING_RULE' : (empty($matched) ? 'MISSING' : 'OK');
    $rows[] = [
        'table' => $table,
        'expected' => implode(', ', $candidates),
        'matched' => implode(', ', $matched),
        'status' => $status,
        'note' => match($table) {
            'penerimaanfinance_detail','partpenerimaan_detail','returnsales_detail','returpenerimaan_detail' => 'Detail represented via parent endpoint; consider dedicated detail route if needed',
            'd_barang' => 'Underlying barang/spareparts dataset; ensure unified endpoint',
            'm_resi' => 'Controller imported but route not declared if MISSING',
            'spv' => 'SPVController imported? Add route if needed',
            default => ''
        }
    ];
}

// Output as formatted table and JSON section.
// Text table
echo "TABLE ROUTE MAPPING REPORT\n";
echo str_repeat('=', 80) . "\n";
printf("%-22s %-35s %-35s %-10s %s\n", 'TABLE', 'EXPECTED_ENDPOINTS', 'MATCHED_ROUTES', 'STATUS', 'NOTE');
echo str_repeat('-', 140) . "\n";
foreach ($rows as $r) {
    printf("%-22s %-35s %-35s %-10s %s\n",
        $r['table'],
        substr($r['expected'],0,35),
        substr($r['matched'],0,35),
        $r['status'],
        $r['note']
    );
}

echo "\nJSON_SUMMARY\n";
echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";

// Exit code: 0 if all OK or detail-only tables missing; 1 if any core table missing.
$hardMissing = array_filter($rows, fn($r)=>$r['status']==='MISSING' && !str_contains($r['note'],'Detail represented'));
if (!empty($hardMissing)) {
    exit(1);
}
exit(0);
