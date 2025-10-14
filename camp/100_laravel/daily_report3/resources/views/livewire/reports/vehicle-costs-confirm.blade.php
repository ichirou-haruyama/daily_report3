<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{layout, title, state};

layout('components.layouts.app');
title('車両費 入力内容の確認');

state([
    'data' => [],
]);

$mount = function () {
    $stored = session()->get('vehicle_cost_input');
    if (!$stored) {
        return redirect()->route('reports.vehicle_costs');
    }
    $this->data = $stored;
};

$back = function () {
    return redirect()->route('reports.vehicle_costs');
};

$proceed = function () {
    // この後、日報本体の保存フローへ接続する想定
    return redirect()->route('reports.other');
};
?>

<section class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">車両費 入力内容の確認</h1>
        <a href="{{ route('reports.create') }}" class="text-sm text-blue-600 hover:underline">日報作成に戻る</a>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg p-5 space-y-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-500">出発地</dt>
                <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['fromLocation'] ?? '' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">到着地</dt>
                <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['toLocation'] ?? '' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500">移動距離</dt>
                <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['distanceKm'] ?? '' }} km</dd>
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
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['tollEntry'] ?? '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">退場IC</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['tollExit'] ?? '' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs text-gray-500">高速料金</dt>
                    <dd class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ number_format((int) ($data['tollAmount'] ?? 0)) }} 円</dd>
                </div>
            @endif
        </dl>

        <div class="flex gap-3">
            <button type="button" wire:click="back"
                class="inline-flex items-center rounded-md bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">戻って修正する</button>
            <button type="button" wire:click="proceed"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">確定して次へ</button>
        </div>
    </div>
</section>
