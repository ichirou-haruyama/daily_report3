<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{layout, title, state};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

layout('components.layouts.app');
title('車両費 入力内容の確認');

state([
    'data' => [],
    'daily' => [],
    'vehicleName' => '',
]);

$boot = function () {
    $stored = session()->get('vehicle_cost_input');
    if (!$stored) {
        return redirect()->route('reports.vehicle_costs');
    }
    $this->data = $stored;

    $daily = session()->get('daily_report_input');
    if (!$daily) {
        return redirect()->route('reports.create');
    }
    $this->daily = $daily;

    if (!empty($stored['vehicleId'])) {
        $v = \App\Models\Vehicle::find((int) $stored['vehicleId']);
        $this->vehicleName = $v?->name ?? '';
    }
};

$back = function () {
    return redirect()->route('reports.vehicle_costs');
};

$proceed = function () {
    $daily = $this->daily;
    $vehicle = $this->data;
    if (!$daily || !$vehicle) {
        return redirect()->route('reports.create');
    }

    $userId = (int) Auth::id();
    DB::transaction(function () use ($userId, $daily, $vehicle) {
        $report = \App\Models\DailyReport::create([
            'user_id' => $userId,
            'report_date' => now('Asia/Tokyo')->toDateString(),
            'construction_id' => (string) ($daily['constructionId'] ?? ''),
            'work_summary' => (string) ($daily['workSummary'] ?? ''),
            'start_time' => filled($daily['startTime'] ?? null) ? $daily['startTime'] . ':00' : null,
            'end_time' => filled($daily['endTime'] ?? null) ? $daily['endTime'] . ':00' : null,
            'total_hours' => is_numeric($daily['adjustedHours'] ?? null) ? (float) $daily['adjustedHours'] : null,
            'status' => 'submitted',
        ]);
        // 車両未使用の場合は明細作成をスキップ
        if (!($vehicle['noVehicleUsed'] ?? false)) {
            \App\Models\DailyReportVehicleCost::create([
                'daily_report_id' => (int) $report->id,
                'vehicle_id' => $vehicle['vehicleId'] ?? null,
                'outbound_start' => filled($vehicle['moveStart'] ?? null) ? $vehicle['moveStart'] . ':00' : null,
                'outbound_end' => filled($vehicle['moveEnd'] ?? null) ? $vehicle['moveEnd'] . ':00' : null,
                'from_location' => $vehicle['fromLocation'] ?? null,
                'to_location' => $vehicle['toLocation'] ?? null,
                'distance_km' => is_numeric($vehicle['distanceKm'] ?? null) ? (float) $vehicle['distanceKm'] : null,
                'toll_not_used' => (bool) ($vehicle['tollNotUsed'] ?? false),
                'toll_entry' => $vehicle['tollEntry'] ?? null,
                'toll_exit' => $vehicle['tollExit'] ?? null,
                'toll_amount' => is_numeric($vehicle['tollAmount'] ?? null) ? (int) $vehicle['tollAmount'] : null,
                'return_start' => filled($vehicle['returnMoveStart'] ?? null) ? $vehicle['returnMoveStart'] . ':00' : null,
                'return_end' => filled($vehicle['returnMoveEnd'] ?? null) ? $vehicle['returnMoveEnd'] . ':00' : null,
                'return_from_location' => $vehicle['returnFromLocation'] ?? null,
                'return_to_location' => $vehicle['returnToLocation'] ?? null,
                'return_distance_km' => is_numeric($vehicle['returnDistanceKm'] ?? null) ? (float) $vehicle['returnDistanceKm'] : null,
                'return_toll_not_used' => (bool) ($vehicle['returnTollNotUsed'] ?? false),
                'return_toll_entry' => $vehicle['returnTollEntry'] ?? null,
                'return_toll_exit' => $vehicle['returnTollExit'] ?? null,
                'return_toll_amount' => is_numeric($vehicle['returnTollAmount'] ?? null) ? (int) $vehicle['returnTollAmount'] : null,
            ]);
        }
    });

    session()->forget('daily_report_input');
    session()->forget('vehicle_cost_input');
    session()->flash('status', '日報を保存しました');
    return redirect()->route('reports.search');
};
?>

<section class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">車両費 入力内容の確認</h1>
        <a href="{{ route('reports.create') }}" class="text-sm text-blue-600 hover:underline">日報作成に戻る</a>
    </div>

    @php($daily = session()->get('daily_report_input', $daily))
    @php($data = session()->get('vehicle_cost_input', $data))
    @php($vehicleName = !empty($data['vehicleId'] ?? null) ? \App\Models\Vehicle::find((int) $data['vehicleId'])?->name ?? '' : $vehicleName ?? '')

    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg p-5 space-y-6">
        <div>
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-2">日報ヘッダ</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs text-gray-500">工事ID</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $daily['constructionId'] ?? '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">直接労務時間</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ isset($daily['adjustedHours']) ? number_format((float) $daily['adjustedHours'], 2) : '' }} h
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">開始時刻</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $daily['startTime'] ?? '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">終了時刻</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $daily['endTime'] ?? '' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-500">作業内容</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100 whitespace-pre-wrap">
                        {{ $daily['workSummary'] ?? '' }}</dd>
                </div>
            </dl>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <dt class="text-xs text-gray-500">使用車両</dt>
                <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                    @if ($data['noVehicleUsed'] ?? false)
                        車両未使用
                    @else
                        {{ $vehicleName ?: '（未選択）' }}
                    @endif
                </dd>
            </div>
            @if (!($data['noVehicleUsed'] ?? false))
                <div>
                    <dt class="text-xs text-gray-500">移動開始時刻</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['moveStart'] ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">到着時刻</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['moveEnd'] ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">出発地</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['fromLocation'] ?? '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">到着地</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['toLocation'] ?? '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">移動距離</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['distanceKm'] ?? '' }} km
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">高速利用</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $data['tollNotUsed'] ?? false ? '未使用' : '使用' }}
                    </dd>
                </div>
                @if (!($data['tollNotUsed'] ?? false))
                    <div>
                        <dt class="text-xs text-gray-500">入場IC</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['tollEntry'] ?? '' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">退場IC</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['tollExit'] ?? '' }}
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-gray-500">高速料金</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ number_format((int) ($data['tollAmount'] ?? 0)) }} 円</dd>
                    </div>
                @endif
            @endif
        </dl>

        @php($hasReturn = !($data['noVehicleUsed'] ?? false) && (filled($data['returnFromLocation'] ?? null) || filled($data['returnToLocation'] ?? null) || filled($data['returnDistanceKm'] ?? null)))
        @if ($hasReturn)
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">復路（帰り）</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs text-gray-500">移動開始時刻（帰り）</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $data['returnMoveStart'] ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">到着時刻（帰り）</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $data['returnMoveEnd'] ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">出発地（帰り）</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $data['returnFromLocation'] ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">到着地（帰り）</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $data['returnToLocation'] ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">移動距離（帰り）</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $data['returnDistanceKm'] ?? '' }} km</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">高速利用（帰り）</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                            {{ $data['returnTollNotUsed'] ?? false ? '未使用' : '使用' }}
                        </dd>
                    </div>
                    @if (!($data['returnTollNotUsed'] ?? false))
                        <div>
                            <dt class="text-xs text-gray-500">入場IC（帰り）</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                {{ $data['returnTollEntry'] ?? '' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">退場IC（帰り）</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                {{ $data['returnTollExit'] ?? '' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-500">高速料金（帰り）</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                {{ number_format((int) ($data['returnTollAmount'] ?? 0)) }} 円</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif

        <div class="flex gap-3">
            <button type="button" wire:click="back"
                class="inline-flex items-center rounded-md bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">戻って修正する</button>
            <button type="button" wire:click="proceed"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">確定して次へ</button>
        </div>
    </div>
</section>
