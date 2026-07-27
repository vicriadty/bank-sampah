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
                'multi_match' => [
                    'query' => $keyword,
                    'fields' => ['nama', 'nik', 'email', 'alamat'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                ],
            ],
        ];

        $result = $this->es->search('nasabahs', $query, $from, $perPage);

        $items = $result['results'];
        $total = $result['total'];

        $ids = array_column($items, 'id');
        $models = Nasabah::whereIn('id', $ids)->get()->keyBy('id');
        $ordered = collect($ids)->map(fn($id) => $models->get($id))->filter();

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
