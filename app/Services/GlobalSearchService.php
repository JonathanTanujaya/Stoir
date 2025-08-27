<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\MBarang;
use App\Models\MCust;
use App\Models\MSupplier;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\MTrans;
use App\Models\KartuStok;
use App\Models\OpName;

class GlobalSearchService
{
    private const CACHE_TTL = 300; // 5 minutes
    private const MAX_RESULTS_PER_CATEGORY = 10;
    
    /**
     * Search configuration untuk each entity
     */
    private $searchConfig = [

        'MCust' => [
            'model' => MCust::class,
            'title_field' => 'NamaCust',
            'subtitle_fields' => ['KodeCust', 'Alamat'],
            'searchable_fields' => ['KodeCust', 'NamaCust', 'Alamat', 'Contact', 'Telp'],
            'route_pattern' => '/master/customers',
            'primary_key' => 'KodeCust',
            'display_name' => 'Customers',
            'icon' => 'people',
            'priority' => 1
        ],
        'MSupplier' => [
            'model' => MSupplier::class,
            'title_field' => 'namasupplier',
            'subtitle_fields' => ['kodesupplier', 'alamat'],
            'searchable_fields' => ['kodesupplier', 'namasupplier', 'alamat', 'contact', 'telp'],
            'route_pattern' => '/master/suppliers',
            'primary_key' => 'kodesupplier',
            'display_name' => 'Suppliers',
            'icon' => 'business',
            'priority' => 1
        ],
        'Invoice' => [
            'model' => Invoice::class,
            'title_field' => 'nomor_invoice',
            'subtitle_fields' => ['tanggal', 'total'],
            'searchable_fields' => ['nomor_invoice', 'kodecust', 'keterangan'],
            'route_pattern' => '/transactions/invoices',
            'primary_key' => 'nomor_invoice',
            'display_name' => 'Invoices',
            'icon' => 'receipt',
            'priority' => 2
        ],
        'Purchase' => [
            'model' => Purchase::class,
            'title_field' => 'nomor_po',
            'subtitle_fields' => ['tanggal', 'kodesupplier'],
            'searchable_fields' => ['nomor_po', 'kodesupplier', 'keterangan'],
            'route_pattern' => '/transactions/purchase',
            'primary_key' => 'nomor_po',
            'display_name' => 'Purchase Orders',
            'icon' => 'shopping_cart',
            'priority' => 2
        ]
    ];

    /**
     * Perform global search across all configured entities
     */
    public function globalSearch(string $query, int $limit = 50): array
    {
        $cacheKey = "global_search:" . md5($query . $limit);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $limit) {
            $results = [];
            $totalResults = 0;

            // Sort entities by priority
            $sortedEntities = collect($this->searchConfig)
                ->sortBy('priority')
                ->toArray();

            foreach ($sortedEntities as $entityName => $config) {
                $entityResults = $this->searchEntity($entityName, $query, self::MAX_RESULTS_PER_CATEGORY);
                
                if (!empty($entityResults['items'])) {
                    $results[$config['display_name']] = $entityResults;
                    $totalResults += $entityResults['count'];
                }
            }

            return [
                'query' => $query,
                'totalResults' => $totalResults,
                'categories' => $results,
                'searchTime' => microtime(true) - LARAVEL_START,
                'cached' => false
            ];
        });
    }

    /**
     * Search within specific entity
     */
    private function searchEntity(string $entityName, string $query, int $limit): array
    {
        $config = $this->searchConfig[$entityName];
        $model = $config['model'];
        
        // Build query dengan fuzzy matching
        $queryBuilder = $model::query();
        
        // Add WHERE clauses untuk each searchable field
        $queryBuilder->where(function ($q) use ($config, $query) {
            foreach ($config['searchable_fields'] as $field) {
                $q->orWhere($field, 'LIKE', "%{$query}%");
            }
        });

        // Execute query dengan pagination calculation
        $totalCount = $queryBuilder->count();
        $items = $queryBuilder->limit($limit)->get();

        $formattedItems = $items->map(function ($item) use ($config, $query) {
            return $this->formatSearchResult($item, $config, $query);
        })->toArray();

        return [
            'count' => $totalCount,
            'items' => $formattedItems,
            'hasMore' => $totalCount > $limit
        ];
    }

    /**
     * Format individual search result dengan deep linking
     */
    private function formatSearchResult($item, array $config, string $query): array
    {
        $primaryKey = $item->{$config['primary_key']};
        
        // Calculate pagination page (assuming 10 items per page)
        $itemsPerPage = 10;
        $allItems = $config['model']::orderBy($config['primary_key'])->pluck($config['primary_key']);
        $itemIndex = $allItems->search($primaryKey);
        $page = ceil(($itemIndex + 1) / $itemsPerPage);

        // Build subtitle dari configured fields
        $subtitleParts = [];
        foreach ($config['subtitle_fields'] as $field) {
            if ($item->$field) {
                $subtitleParts[] = $item->$field;
            }
        }

        // Identify matched fields untuk highlighting
        $matchedFields = [];
        foreach ($config['searchable_fields'] as $field) {
            if ($item->$field && stripos($item->$field, $query) !== false) {
                $matchedFields[] = $field;
            }
        }

        return [
            'id' => $primaryKey,
            'title' => $item->{$config['title_field']},
            'subtitle' => implode(' • ', $subtitleParts),
            'deepLink' => $config['route_pattern'] . "?page={$page}&highlight={$primaryKey}",
            'matchedFields' => $matchedFields,
            'icon' => $config['icon'],
            'category' => $config['display_name'],
            'relevanceScore' => $this->calculateRelevance($item, $config, $query)
        ];
    }

    /**
     * Calculate relevance score untuk result ranking
     */
    private function calculateRelevance($item, array $config, string $query): float
    {
        $score = 0;
        $queryLower = strtolower($query);

        foreach ($config['searchable_fields'] as $field) {
            $fieldValue = strtolower($item->$field ?? '');
            
            if ($fieldValue === $queryLower) {
                $score += 100; // Exact match
            } elseif (strpos($fieldValue, $queryLower) === 0) {
                $score += 75; // Starts with query
            } elseif (strpos($fieldValue, $queryLower) !== false) {
                $score += 50; // Contains query
            }
        }

        // Boost score untuk primary fields
        if (in_array($config['title_field'], $config['searchable_fields'])) {
            $titleValue = strtolower($item->{$config['title_field']} ?? '');
            if (strpos($titleValue, $queryLower) !== false) {
                $score *= 1.5; // 50% boost for title matches
            }
        }

        return round($score, 2);
    }

    /**
     * Get search suggestions untuk autocomplete
     */
    public function getSearchSuggestions(string $query, int $limit = 5): array
    {
        $cacheKey = "search_suggestions:" . md5($query . $limit);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($query, $limit) {
            $suggestions = [];

            // Get suggestions dari most commonly searched fields
            $commonFields = [
                'MBarang' => 'namabarang',
                'MCust' => 'namacust',
                'MSupplier' => 'namasupplier'
            ];

            foreach ($commonFields as $entityName => $field) {
                $config = $this->searchConfig[$entityName];
                $model = $config['model'];
                
                $items = $model::where($field, 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->pluck($field)
                    ->unique()
                    ->take($limit);

                foreach ($items as $suggestion) {
                    $suggestions[] = [
                        'text' => $suggestion,
                        'category' => $config['display_name'],
                        'icon' => $config['icon']
                    ];
                }
            }

            return array_slice($suggestions, 0, $limit);
        });
    }

    /**
     * Track search analytics
     */
    public function trackSearch(string $query, int $resultsCount, float $searchTime): void
    {
        DB::table('search_analytics')->insert([
            'query' => $query,
            'results_count' => $resultsCount,
            'search_time_ms' => round($searchTime * 1000, 2),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Clear search cache
     */
    public function clearSearchCache(): void
    {
        Cache::flush();
    }
}
