<?php

namespace App\Services\Search;

use App\Models\Nasabah;
use App\Services\ElasticsearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class NasabahSearchRepository
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
                            'nama' => ['query' => $keyword, 'operator' => 'and', 'boost' => 5],
                        ]],
                        ['match' => [
                            'nik' => ['query' => $keyword, 'operator' => 'and', 'boost' => 4],
                        ]],
                        ['prefix' => ['nama' => ['value' => $keyword, 'boost' => 3]]],
                        ['prefix' => ['nik' => ['value' => $keyword, 'boost' => 2]]],
                        ['multi_match' => [
                            'query' => $keyword,
                            'fields' => ['nama^2', 'nik^2', 'email', 'alamat^0.5'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO',
                        ]],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
        ];

        $result = $this->es->search('nasabahs', $query, $from, $perPage);

        $items = $result['results'];
        $total = $result['total'];

        $ids = array_column($items, 'id');
        $models = Nasabah::whereIn('id', $ids)->get()->keyBy('id');
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
        $query = Nasabah::where('nama', 'like', "%{$keyword}%")
            ->orWhere('nik', 'like', "%{$keyword}%")
            ->orWhere('email', 'like', "%{$keyword}%")
            ->orWhere('alamat', 'like', "%{$keyword}%");

        return [
            'engine' => 'mysql',
            'result' => $query->paginate($perPage, ['*'], 'page', $page),
        ];
    }
}
