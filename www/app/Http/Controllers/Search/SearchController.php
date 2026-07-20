<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Services\RedisService;
use App\Services\Search\JenisSampahSearchRepository;
use App\Services\Search\NasabahSearchRepository;
use App\Services\Search\SetoranSearchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $result = $this->redis->remember($cacheKey, 300, function () use ($repo, $keyword, $perPage, $page) {
            $result = $repo->search($keyword, $perPage, $page);

            return [
                'data' => $result->items(),
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
            ];
        });

        return response()->json($result);
    }
}
