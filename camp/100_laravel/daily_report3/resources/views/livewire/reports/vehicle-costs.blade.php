<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{layout, title, state, rules};

layout('components.layouts.app');
title('車両費 入力');

state([
    'vehicleId' => null,
    'moveStart' => '',
    'moveEnd' => '',
    'fromLocation' => '',
    'toLocation' => '',
    'distanceKm' => '',
    'tollNotUsed' => false,
    'tollEntry' => '',
    'tollExit' => '',
    'tollAmount' => '',
    'locked' => false,
]);

rules([
    'vehicleId' => ['nullable', 'integer'],
    'moveStart' => ['nullable', 'string'],
    'moveEnd' => ['nullable', 'string'],
    'fromLocation' => ['nullable', 'string', 'max:191'],
    'toLocation' => ['nullable', 'string', 'max:191'],
    'distanceKm' => ['nullable', 'numeric', 'min:0'],
    'tollEntry' => ['nullable', 'string', 'max:191'],
    'tollExit' => ['nullable', 'string', 'max:191'],
    'tollAmount' => ['nullable', 'numeric', 'min:0'],
]);

$selectVehicle = function (int $vehicleId) {
    if ($this->locked) {
        return;
    }
    $this->vehicleId = $vehicleId;
};

$goToConfirm = function () {
    // 必須バリデーション
    $baseRules = [
        'fromLocation' => ['required', 'string', 'max:191'],
        'toLocation' => ['required', 'string', 'max:191'],
        'distanceKm' => ['required', 'numeric', 'min:0'],
    ];

    if ($this->tollNotUsed) {
        $rules = $baseRules; // 高速未使用の場合は高速関連は任意
    } else {
        $rules = array_merge($baseRules, [
            'tollEntry' => ['required', 'string', 'max:191'],
            'tollExit' => ['required', 'string', 'max:191'],
            'tollAmount' => ['required', 'numeric', 'min:0'],
        ]);
    }

    $this->validate($rules);

    session()->put('vehicle_cost_input', [
        'vehicleId' => $this->vehicleId,
        'moveStart' => $this->moveStart,
        'moveEnd' => $this->moveEnd,
        'fromLocation' => $this->fromLocation,
        'toLocation' => $this->toLocation,
        'distanceKm' => $this->distanceKm,
        'tollNotUsed' => $this->tollNotUsed,
        'tollEntry' => $this->tollEntry,
        'tollExit' => $this->tollExit,
        'tollAmount' => $this->tollAmount,
    ]);

    return redirect()->route('reports.vehicle_costs.confirm');
};
?>

<section class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">車両費 入力</h1>
        <a href="{{ route('reports.create') }}" class="text-sm text-blue-600 hover:underline">日報作成に戻る</a>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg p-5 space-y-8">
        @php($vehicles = \App\Models\Vehicle::where('is_active', true)->orderBy('name')->get())

        <div class="space-y-3">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">使用車両を選択</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @forelse ($vehicles as $v)
                    <button type="button" wire:click="selectVehicle({{ $v->id }})"
                        class="rounded-lg border px-3 py-3 text-sm font-medium transition
                        {{ $vehicleId === $v->id ? 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-200' : 'border-gray-300 dark:border-gray-700 dark:text-gray-100' }}
                        {{ $locked ? 'opacity-60 cursor-not-allowed' : 'hover:border-emerald-400' }}"
                        @disabled($locked)>
                        {{ $v->name }}
                    </button>
                @empty
                    <div class="text-sm text-gray-500 col-span-4">車両マスタが未登録です。</div>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">移動開始時間</label>
                <input type="time" step="300" wire:model.live="moveStart"
                    class="mt-1 block w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    @disabled($locked) />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">到着時刻</label>
                <input type="time" step="300" wire:model.live="moveEnd"
                    class="mt-1 block w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    @disabled($locked) />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">出発地</label>
                <input type="text" wire:model.live="fromLocation" maxlength="191" placeholder="例）本社 倉庫"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    @disabled($locked) />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">到着地</label>
                <input type="text" wire:model.live="toLocation" maxlength="191" placeholder="例）盛岡第一現場"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    @disabled($locked) />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">移動距離（km）</label>
                <input type="number" min="0" step="0.1" inputmode="decimal" wire:model.live="distanceKm"
                    class="mt-1 block w-48 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    @disabled($locked) />
            </div>
        </div>

        <div class="space-y-3">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">高速道路の利用</h2>
            <label class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-100">
                <input type="checkbox" wire:model.live="tollNotUsed"
                    class="rounded border-gray-300 dark:border-gray-700" />
                高速道路は使用していない
            </label>

            @if (!$tollNotUsed)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">入場IC</label>
                        <input type="text" wire:model.live="tollEntry" maxlength="191" placeholder="例）盛岡南 IC"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                    </div>
                    @if (filled($tollEntry))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">退場IC</label>
                            <input type="text" wire:model.live="tollExit" maxlength="191" placeholder="例）紫波 IC"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                        </div>
                    @endif
                    @if (filled($tollEntry) && filled($tollExit))
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">高速料金（円）</label>
                            <input type="number" min="0" step="1" inputmode="numeric"
                                wire:model.live="tollAmount"
                                class="mt-1 block w-48 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                        </div>
                    @endif
                </div>
            @endif

            @if ($tollNotUsed || (filled($tollEntry) && filled($tollExit) && filled($tollAmount)))
                <div>
                    <button type="button" wire:click="goToConfirm"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        入力内容を確認する
                    </button>
                </div>
            @endif
        </div>

        <div class="flex gap-3">
            <a href="{{ route('reports.create') }}"
                class="inline-flex items-center rounded-md bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">日報作成に戻る</a>
        </div>
    </div>

</section>
