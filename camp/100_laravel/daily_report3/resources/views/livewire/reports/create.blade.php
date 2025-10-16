<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{layout, state, rules, title};

layout('components.layouts.app');
title('作業日報 作成');

state([
    'constructionId' => '', // 検索画面からの工事ID
    'startTime' => '', // HH:MM
    'endTime' => '', // HH:MM
    'adjustedHours' => null, // 自動計算された労務時間（昼休/小休控除後）
    'showDetails' => false, // 時間入力後に作業内容を表示
    'workSummary' => '',
]);

rules([
    'constructionId' => ['nullable', 'string', 'max:64'],
    'startTime' => ['nullable', 'string'],
    'endTime' => ['nullable', 'string'],
    'workSummary' => ['nullable', 'string', 'max:2000'],
]);

/**
 * 初期化時にクエリパラメータの工事IDを保持
 */
$boot = function () {
    $cid = (string) request()->query('construction_id', '');
    if ($cid !== '') {
        $this->constructionId = $cid;
    }
};

/**
 * HH:MM を分に変換
 */
$toMinutes = function (?string $hhmm): ?int {
    if (!$hhmm) {
        return null;
    }
    $parts = explode(':', $hhmm);
    if (count($parts) !== 2) {
        return null;
    }
    $h = (int) $parts[0];
    $m = (int) $parts[1];
    if ($h < 0 || $h > 23 || $m < 0 || $m > 59) {
        return null;
    }
    return $h * 60 + $m;
};

/**
 * 昼休（12:00-13:00）と小休（10:00-10:15, 15:00-15:15）を自動控除して労務時間を計算
 */
$recalculate = function () use ($toMinutes) {
    $start = $toMinutes($this->startTime);
    $end = $toMinutes($this->endTime);

    $this->adjustedHours = null;
    $this->showDetails = false;

    if ($start === null || $end === null) {
        return;
    }
    if ($end <= $start) {
        // 同日内前提。終了が開始以下なら非表示のまま
        return;
    }

    $total = $end - $start; // 分

    // 控除対象の時間帯（分）: 昼休 + 小休2回
    $breakWindows = [
        [12 * 60, 13 * 60], // 12:00 - 13:00
        [10 * 60, 10 * 60 + 15], // 10:00 - 10:15
        [15 * 60, 15 * 60 + 15], // 15:00 - 15:15
    ];

    $overlap = 0;
    foreach ($breakWindows as [$bStart, $bEnd]) {
        $overlap += max(0, min($end, $bEnd) - max($start, $bStart));
    }
    $adjusted = max(0, $total - $overlap);

    $this->adjustedHours = round($adjusted / 60, 2);
    $this->showDetails = true;
};

/**
 * 車両費入力へ進む前に、日報入力内容をセッションへ保存
 */
$proceedToVehicleCosts = function () {
    // 表示上は $canProceed でガード済みだが、念のため簡易バリデーション
    $this->validate([
        'workSummary' => ['required', 'string', 'max:2000'],
    ]);

    session()->put('daily_report_input', [
        'constructionId' => $this->constructionId,
        'startTime' => $this->startTime,
        'endTime' => $this->endTime,
        'adjustedHours' => $this->adjustedHours,
        'workSummary' => $this->workSummary,
    ]);

    return redirect()->route('reports.vehicle_costs');
};
?>

<section class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">作業日報 作成</h1>
        <a href="{{ route('reports.search') }}" class="text-sm text-blue-600 hover:underline">検索に戻る</a>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg p-5 space-y-6">

        @if (filled($constructionId))
            <div class="text-sm text-gray-600 dark:text-gray-300">
                選択中の工事ID: <span class="font-medium">{{ $constructionId }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">開始時刻</label>
                <input type="time" step="300" wire:model.live="startTime" wire:change="recalculate"
                    class="mt-1 block w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">終了時刻</label>
                <input type="time" step="300" wire:model.live="endTime" wire:change="recalculate"
                    class="mt-1 block w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            </div>
        </div>

        @if (!is_null($adjustedHours))
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">直接労務時間（自動計算）</label>
                <input type="text" readonly value="{{ number_format($adjustedHours, 2) }}"
                    class="mt-1 block w-40 rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/60 dark:text-gray-100" />
                <p class="text-xs text-gray-500">昼休（12:00〜13:00）と小休（10:00〜10:15／15:00〜15:15）を自動控除しています。</p>
            </div>
        @endif

        @if ($showDetails)
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">作業内容（最大2000文字）</label>
                <textarea wire:model.live="workSummary" maxlength="2000" rows="6"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    placeholder="本日の作業内容を入力してください"></textarea>
                <div class="text-xs text-gray-500 text-right">{{ mb_strlen($workSummary) }}/2000</div>
            </div>
        @endif

        @php($canProceed = $showDetails && filled($workSummary))
        @if ($canProceed)
            <div class="flex gap-3">
                <button type="button" wire:click="proceedToVehicleCosts"
                    class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">車両費を入力する</button>
                <a href="{{ route('reports.other') }}"
                    class="inline-flex items-center rounded-md bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">車両費を入力せずその他へ</a>
            </div>
        @endif
    </div>
</section>
