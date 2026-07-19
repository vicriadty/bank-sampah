<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Services\Search\JenisSampahSearchRepository;
use App\Services\Search\NasabahSearchRepository;
use App\Services\Search\SetoranSearchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function nasabah(Request $request, NasabahSearchRepository $repo): JsonResponse
    {
        $keyword = $request->input('q', '');
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        if (empty($keyword)) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $result = $repo->search($keyword, $perPage, $page);

        return response()->json([
            'data' => $result->items(),
            'total' => $result->total(),
            'per_page' => $result->perPage(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
        ]);
    }

    public function sampah(Request $request, JenisSampahSearchRepository $repo): JsonResponse
    {
        $keyword = $request->input('q', '');
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        if (empty($keyword)) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $result = $repo->search($keyword, $perPage, $page);

        return response()->json([
            'data' => $result->items(),
            'total' => $result->total(),
            'per_page' => $result->perPage(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
        ]);
    }

    public function setoran(Request $request, SetoranSearchRepository $repo): JsonResponse
    {
        $keyword = $request->input('q', '');
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        if (empty($keyword)) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $result = $repo->search($keyword, $perPage, $page);

        return response()->json([
            'data' => $result->items(),
            'total' => $result->total(),
            'per_page' => $result->perPage(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
        ]);
    }
}
