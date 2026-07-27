<?php

namespace App\Http\Controllers\Search;

use App\Exceptions\SearchUnavailableException;
use App\Http\Controllers\Controller;
use App\Services\RedisService;
use App\Services\Search\JenisSampahSearchRepository;
use App\Services\Search\NasabahSearchRepository;
use App\Services\Search\SetoranSearchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function __construct(
        private RedisService $redis
    ) {}

    public function nasabah(Request $request, NasabahSearchRepository $repo): JsonResponse
    {
        return $this->search($request, $repo, 'nasabah');
    }

    public function sampah(Request $request, JenisSampahSearchRepository $repo): JsonResponse
    {
        return $this->search($request, $repo, 'sampah');
    }

    public function setoran(Request $request, SetoranSearchRepository $repo): JsonResponse
    {
        return $this->search($request, $repo, 'setoran');
    }

    private function search(Request $request, object $repo, string $type): JsonResponse
    {
        $keyword = $request->input('q', '');
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        if (empty($keyword)) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $cacheKey = "search:{$type}:" . md5($keyword) . ":page{$page}";
        $cacheStatus = 'miss';
        $startTime = microtime(true);

        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            $cacheStatus = 'hit';
            $result = $cached;
        } else {
            try {
                $searchResult = $repo->search($keyword, $perPage, $page);

                $result = [
                    'engine' => $searchResult['engine'],
                    'data' => $searchResult['result']->items(),
                    'total' => $searchResult['result']->total(),
                    'per_page' => $searchResult['result']->perPage(),
                    'current_page' => $searchResult['result']->currentPage(),
                    'last_page' => $searchResult['result']->lastPage(),
                ];
            } catch (SearchUnavailableException $e) {
                Log::channel('elasticsearch')->warning('ES unavailable, falling back to MySQL: ' . $e->getMessage());

                $searchResult = $repo->mysqlFallback($keyword, $perPage, $page);

                $result = [
                    'engine' => 'mysql',
                    'data' => $searchResult['result']->items(),
                    'total' => $searchResult['result']->total(),
                    'per_page' => $searchResult['result']->perPage(),
                    'current_page' => $searchResult['result']->currentPage(),
                    'last_page' => $searchResult['result']->lastPage(),
                ];
            }

            Cache::put($cacheKey, $result, 300);
        }

        $elapsed = round((microtime(true) - $startTime) * 1000, 2);
        $engine = $result['engine'];

        if (config('app.debug')) {
            $this->logSearch($keyword, $engine, $type, $cacheStatus, $elapsed);
        }

        $response = response()->json([
            'search_engine' => $engine,
            'cache_status' => strtoupper($cacheStatus),
            'search_time' => $elapsed . ' ms',
            'data' => $result['data'],
            'total' => $result['total'],
            'per_page' => $result['per_page'],
            'current_page' => $result['current_page'],
            'last_page' => $result['last_page'],
        ]);

        $response->headers->set('X-Cache', strtoupper($cacheStatus));
        $servedFrom = $cacheStatus === 'hit' ? 'Redis' : ucfirst($engine);
        $response->headers->set('X-Served-From', $servedFrom);
        $response->headers->set('X-Search-Time', $elapsed . ' ms');

        return $response;
    }

    private function logSearch(string $keyword, string $engine, string $type, string $cache, float $elapsed): void
    {
        Log::channel('search')->info('Search executed', [
            'keyword' => $keyword,
            'type' => $type,
            'engine' => $engine,
            'cache' => strtoupper($cache),
            'time' => $elapsed . ' ms',
        ]);
    }
}
