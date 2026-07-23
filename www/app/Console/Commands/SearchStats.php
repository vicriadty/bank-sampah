<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SearchStats extends Command
{
    protected $signature = 'search:stats';
    protected $description = 'Tampilkan statistik pencarian dari log';

    public function handle(): int
    {
        $logPath = storage_path('logs/search.log');

        if (!File::exists($logPath)) {
            $this->warn('Log file tidak ditemukan: storage/logs/search.log');
            return Command::SUCCESS;
        }

        $lines = File::lines($logPath)->filter(fn($line) => !empty(trim($line)))->toArray();

        $totalSearches = 0;
        $redisHit = 0;
        $redisMiss = 0;
        $engineElasticsearch = 0;
        $engineMysql = 0;
        $totalTime = 0;
        $timeCount = 0;
        $keywords = [];

        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if (!$data || !isset($data['context'])) {
                continue;
            }

            $context = $data['context'];
            if (!isset($context['keyword'])) {
                continue;
            }

            $totalSearches++;

            $cache = strtoupper($context['cache'] ?? '');
            if ($cache === 'HIT') {
                $redisHit++;
            } elseif ($cache === 'MISS') {
                $redisMiss++;
            }

            $engine = $context['engine'] ?? '';
            if ($engine === 'elasticsearch') {
                $engineElasticsearch++;
            } elseif ($engine === 'mysql') {
                $engineMysql++;
            }

            $timeStr = $context['time'] ?? '';
            if (preg_match('/([\d.]+)\s*ms/', $timeStr, $m)) {
                $totalTime += (float) $m[1];
                $timeCount++;
            }

            $kw = $context['keyword'] ?? '';
            if (!empty($kw)) {
                $keywords[$kw] = ($keywords[$kw] ?? 0) + 1;
            }
        }

        $avgTime = $timeCount > 0 ? round($totalTime / $timeCount, 2) : 0;

        $this->newLine();
        $this->info('Search Statistics');
        $this->newLine();
        $this->line(" Total Searches : {$totalSearches}");
        $this->line(" Redis HIT      : {$redisHit}");
        $this->line(" Redis MISS     : {$redisMiss}");
        $this->line(" Elasticsearch  : {$engineElasticsearch}");
        $this->line(" MySQL Fallback : {$engineMysql}");
        $this->line(" Average Time   : {$avgTime} ms");
        $this->newLine();

        if (!empty($keywords)) {
            arsort($keywords);
            $top = array_slice($keywords, 0, 5, true);

            $this->info('Top 5 Keywords:');
            foreach ($top as $kw => $count) {
                $this->line("   \"{$kw}\" — {$count}x");
            }
            $this->newLine();
        }

        return Command::SUCCESS;
    }
}
