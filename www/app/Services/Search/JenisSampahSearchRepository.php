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

    public function search(string $keyword, int $perPage = 10, int $page = 1): array
    {
        $from = ($page - 1) * $perPage;

        $query = [
            'query' => [
                'bool' => [
                    'should' => [
                        ['match' => [
                            'nama_jenis' => ['query' => $keyword, 'operator' => 'and', 'boost' => 5],
                        ]],
                        ['prefix' => ['nama_jenis' => ['value' => $keyword, 'boost' => 3]]],
                        ['multi_match' => [
                            'query' => $keyword,
                            'fields' => ['nama_jenis^2', 'kategori'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO',
                        ]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
        ];

        $result = $this->es->search('jenis_sampahs', $query, $from, $perPage);

        $items = $result['results'];
        $total = $result['total'];

        $ids = array_column($items, 'id');
        $models = JenisSampah::with('kategoriSampah')->whereIn('id', $ids)->get()->keyBy('id');
        $ordered = collect($ids)->map(fn($id) => $models->get($id))->filter()->values();

        return [
            'engine' => 'elasticsearch',
            'result' => new LengthAwarePaginator(
                $ordered,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            ),
        ];
    }

    public function mysqlFallback(string $keyword, int $perPage, int $page): array
    {
        $query = JenisSampah::with('kategoriSampah')
            ->where('nama_jenis', 'like', "%{$keyword}%")
            ->orWhereHas('kategoriSampah', fn($q) => $q->where('nama_kategori', 'like', "%{$keyword}%"));

        return [
            'engine' => 'mysql',
            'result' => $query->paginate($perPage, ['*'], 'page', $page),
        ];
    }
}
