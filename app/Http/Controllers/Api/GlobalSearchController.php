<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GlobalSearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GlobalSearchController extends Controller
{
    private GlobalSearchService $searchService;

    public function __construct(GlobalSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Global search endpoint
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2|max:100',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $startTime = microtime(true);
            $query = trim($request->get('q'));
            $limit = $request->get('limit', 50);

            $results = $this->searchService->globalSearch($query, $limit);
            
            $searchTime = microtime(true) - $startTime;

            // Track analytics
            $this->searchService->trackSearch($query, $results['totalResults'], $searchTime);

            return response()->json([
                'success' => true,
                'data' => $results,
                'meta' => [
                    'searchTime' => round($searchTime * 1000, 2) . 'ms',
                    'query' => $query,
                    'limit' => $limit
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Global search error', [
                'query' => $request->get('q'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Search service temporarily unavailable',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Search suggestions untuk autocomplete
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function suggestions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:1|max:50',
            'limit' => 'sometimes|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $query = trim($request->get('q'));
            $limit = $request->get('limit', 5);

            $suggestions = $this->searchService->getSearchSuggestions($query, $limit);

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            \Log::error('Search suggestions error', [
                'query' => $request->get('q'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Suggestions service unavailable'
            ], 500);
        }
    }

    /**
     * Clear search cache (admin only)
     * 
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->searchService->clearSearchCache();

            return response()->json([
                'success' => true,
                'message' => 'Search cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    /**
     * Search analytics endpoint
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $analytics = \DB::table('search_analytics')
                ->selectRaw('
                    COUNT(*) as total_searches,
                    AVG(results_count) as avg_results,
                    AVG(search_time_ms) as avg_search_time,
                    query,
                    COUNT(query) as search_frequency
                ')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('query')
                ->orderBy('search_frequency', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'topQueries' => $analytics,
                    'summary' => [
                        'totalSearches' => $analytics->sum('search_frequency'),
                        'avgSearchTime' => round($analytics->avg('avg_search_time'), 2),
                        'avgResults' => round($analytics->avg('avg_results'), 2)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analytics unavailable'
            ], 500);
        }
    }
}
