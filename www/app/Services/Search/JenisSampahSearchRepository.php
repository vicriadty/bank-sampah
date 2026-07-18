<?php

namespace App\Services\Search;

use App\Models\JenisSampah;
use App\Services\ElasticsearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class JenisSampahSearchRepository
{
    public function __construct(
        private ElasticsearchService $es
    ) {}

    public function search(string $keyword, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        try {
            $from = ($page - 1) * $perPage;

            $query = [
                'query' => [
                    'multi_match' => [
                        'query' => $keyword,
                        'fields' => ['nama_jenis', 'kategori'],
                        'type' => 'best_fields',
                    ],
                ],
            ];

            $result = $this->es->search('jenis_sampahs', $query, $from, $perPage);

            $items = $result['results'];
            $total = $result['total'];

            $ids = array_column($items, 'id');
            $models = JenisSampah::with('kategoriSampah')->whereIn('id', $ids)->get()->keyBy('id');
            $ordered = collect($ids)->map(fn($id) => $models->get($id))->filter();

            return new LengthAwarePaginator(
                $ordered,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } catch (\Exception $e) {
            Log::channel('elasticsearch')->warning('ES search failed, falling back to MySQL LIKE: ' . $e->getMessage());
            return $this->mysqlFallback($keyword, $perPage, $page);
        }
    }

    private function mysqlFallback(string $keyword, int $perPage, int $page): LengthAwarePaginator
    {
        $query = JenisSampah::with('kategoriSampah')
            ->where('nama_jenis', 'like', "%{$keyword}%")
            ->orWhereHas('kategoriSampah', fn($q) => $q->where('nama_kategori', 'like', "%{$keyword}%"));

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
