<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{layout, title, state, rules};

layout('components.layouts.app');
title('車両費 入力');

state([
    'vehicleId' => null,
    'noVehicleUsed' => false,
    'moveStart' => '',
    'moveEnd' => '',
    'fromLocation' => '',
    'toLocation' => '',
    'distanceKm' => '',
    'tollNotUsed' => false,
    'tollEntry' => '',
    'tollExit' => '',
    'tollAmount' => '',
    // 復路（帰り）
    'returnMoveStart' => '',
    'returnMoveEnd' => '',
    'returnFromLocation' => '',
    'returnToLocation' => '',
    'returnDistanceKm' => '',
    'returnTollNotUsed' => false,
    'returnTollEntry' => '',
    'returnTollExit' => '',
    'returnTollAmount' => '',
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
    // 復路（帰り）
    'returnMoveStart' => ['nullable', 'string'],
    'returnMoveEnd' => ['nullable', 'string'],
    'returnFromLocation' => ['nullable', 'string', 'max:191'],
    'returnToLocation' => ['nullable', 'string', 'max:191'],
    'returnDistanceKm' => ['nullable', 'numeric', 'min:0'],
    'returnTollEntry' => ['nullable', 'string', 'max:191'],
    'returnTollExit' => ['nullable', 'string', 'max:191'],
    'returnTollAmount' => ['nullable', 'numeric', 'min:0'],
]);

$selectVehicle = function (int $vehicleId) {
    if ($this->locked) {
        return;
    }
    $this->vehicleId = $vehicleId;
};

$copyReturnFromOutbound = function () {
    if ($this->locked) {
        return;
    }
    // 時刻（往路の終了→帰りの開始、往路の開始→帰りの終了）
    $this->returnMoveStart = $this->moveEnd;
    $this->returnMoveEnd = $this->moveStart;

    // 地点（往路の到着→帰りの出発、往路の出発→帰りの到着）
    $this->returnFromLocation = $this->toLocation;
    $this->returnToLocation = $this->fromLocation;

    // 距離はそのままコピー
    $this->returnDistanceKm = $this->distanceKm;

    // 高速利用の有無と詳細
    $this->returnTollNotUsed = $this->tollNotUsed;
    if ($this->tollNotUsed) {
        $this->returnTollEntry = '';
        $this->returnTollExit = '';
        $this->returnTollAmount = '';
    } else {
        $this->returnTollEntry = $this->tollExit;
        $this->returnTollExit = $this->tollEntry;
        $this->returnTollAmount = $this->tollAmount;
    }
};

$goToConfirm = function () {
    // 車両未使用の場合は入力をスキップ
    if ($this->noVehicleUsed) {
        session()->put('vehicle_cost_input', [
            'noVehicleUsed' => true,
        ]);
        return redirect()->route('reports.vehicle_costs.confirm');
    }

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

    // 復路の必須チェック（復路を入力する場合は一式必須）
    $hasReturnAny = filled($this->returnFromLocation) || filled($this->returnToLocation) || filled($this->returnDistanceKm) || filled($this->returnTollEntry) || filled($this->returnTollExit) || filled($this->returnTollAmount);
    if ($hasReturnAny) {
        $returnBase = [
            'returnFromLocation' => ['required', 'string', 'max:191'],
            'returnToLocation' => ['required', 'string', 'max:191'],
            'returnDistanceKm' => ['required', 'numeric', 'min:0'],
        ];
        if ($this->returnTollNotUsed) {
            $rules = array_merge($rules, $returnBase);
        } else {
            $rules = array_merge($rules, $returnBase, [
                'returnTollEntry' => ['required', 'string', 'max:191'],
                'returnTollExit' => ['required', 'string', 'max:191'],
                'returnTollAmount' => ['required', 'numeric', 'min:0'],
            ]);
        }
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
        // 復路（帰り）
        'returnMoveStart' => $this->returnMoveStart,
        'returnMoveEnd' => $this->returnMoveEnd,
        'returnFromLocation' => $this->returnFromLocation,
        'returnToLocation' => $this->returnToLocation,
        'returnDistanceKm' => $this->returnDistanceKm,
        'returnTollNotUsed' => $this->returnTollNotUsed,
        'returnTollEntry' => $this->returnTollEntry,
        'returnTollExit' => $this->returnTollExit,
        'returnTollAmount' => $this->returnTollAmount,
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
            <label class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-100">
                <input type="checkbox" wire:model.live="noVehicleUsed"
                    class="rounded border-gray-300 dark:border-gray-700" />
                車両は使用していない（全入力をスキップ）
            </label>
            @if ($noVehicleUsed)
                <div
                    class="text-sm text-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 dark:text-emerald-200 rounded p-3">
                    車両未使用のため、下記の入力は不要です。「入力内容を確認する」を押して次へ進めます。
                </div>
            @endif
            @if (!$noVehicleUsed)
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
            @endif
        </div>

        @if (!$noVehicleUsed)
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
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">高速料金（円）</label>
                                <input type="number" min="0" step="1" inputmode="numeric"
                                    wire:model.live="tollAmount"
                                    class="mt-1 block w-48 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                            </div>
                        @endif
                    </div>
                @endif


            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">復路（帰り）の入力</h2>
                    <button type="button" wire:click="copyReturnFromOutbound"
                        class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-1.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 hover:bg-emerald-100"
                        @disabled($locked)>
                        往路→復路 逆転コピー
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">移動開始時間（帰り）</label>
                        <input type="time" step="300" wire:model.live="returnMoveStart"
                            class="mt-1 block w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                            @disabled($locked) />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">到着時刻（帰り）</label>
                        <input type="time" step="300" wire:model.live="returnMoveEnd"
                            class="mt-1 block w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                            @disabled($locked) />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">出発地（帰り）</label>
                        <input type="text" wire:model.live="returnFromLocation" maxlength="191" placeholder="例）現場"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                            @disabled($locked) />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">到着地（帰り）</label>
                        <input type="text" wire:model.live="returnToLocation" maxlength="191" placeholder="例）本社"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                            @disabled($locked) />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">移動距離（km, 帰り）</label>
                        <input type="number" min="0" step="0.1" inputmode="decimal"
                            wire:model.live="returnDistanceKm"
                            class="mt-1 block w-48 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                            @disabled($locked) />
                    </div>
                </div>

                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">高速道路の利用（帰り）</h3>
                <label class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-100">
                    <input type="checkbox" wire:model.live="returnTollNotUsed"
                        class="rounded border-gray-300 dark:border-gray-700" />
                    高速道路は使用していない（帰り）
                </label>

                @if (!$returnTollNotUsed)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">入場IC（帰り）</label>
                            <input type="text" wire:model.live="returnTollEntry" maxlength="191"
                                placeholder="例）紫波 IC"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                        </div>
                        @if (filled($returnTollEntry))
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">退場IC（帰り）</label>
                                <input type="text" wire:model.live="returnTollExit" maxlength="191"
                                    placeholder="例）盛岡南 IC"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                            </div>
                        @endif
                        @if (filled($returnTollEntry) && filled($returnTollExit))
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">高速料金（円,
                                    帰り）</label>
                                <input type="number" min="0" step="1" inputmode="numeric"
                                    wire:model.live="returnTollAmount"
                                    class="mt-1 block w-48 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <div class="flex gap-3">
            <button type="button" wire:click="goToConfirm"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                入力内容を確認する
            </button>
            <a href="{{ route('reports.create') }}"
                class="inline-flex items-center rounded-md bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">日報作成に戻る</a>
        </div>
    </div>

</section>
