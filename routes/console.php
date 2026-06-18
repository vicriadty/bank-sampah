<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan auto-convert saldo rupiah ke emas setiap pukul 00:00
Schedule::command('gold:auto-convert')->dailyAt('00:00');
