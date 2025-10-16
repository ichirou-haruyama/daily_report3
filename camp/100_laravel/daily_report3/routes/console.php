<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Carbon;
use App\Models\DailyReport;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 90日を超えた日報関連データを削除
Artisan::command('reports:prune-old', function () {
    $cutoff = Carbon::now()->subDays(90)->toDateString();
    $deleted = DailyReport::whereDate('report_date', '<', $cutoff)->delete();
    $this->info("Deleted {$deleted} old daily reports before {$cutoff}.");
})->purpose('Delete daily reports older than 90 days');

// 毎日深夜に自動実行
Schedule::command('reports:prune-old')->dailyAt('02:30');
