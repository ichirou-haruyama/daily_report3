<?php

use App\Services\SheetSearchService;
use Livewire\Volt\Component;
use function Livewire\Volt\{layout, state, rules, title};

layout('components.layouts.app');
title('作業日報検索');

state([
    'constructionId' => '',
    'site' => '',
    'subject' => '',
    'results' => [],
    'isSearching' => false,
    'error' => '',
    'hasSearched' => false,
]);

rules([
    'constructionId' => ['nullable', 'string', 'max:64'],
    'site' => ['nullable', 'string', 'max:100'],
    'subject' => ['nullable', 'string', 'max:100'],
]);

$search = function (SheetSearchService $svc) {
    $this->validate();
    $this->isSearching = true;
    $this->error = '';
    try {
        $this->results = $svc->search($this->constructionId ?: null, $this->site ?: null, $this->subject ?: null);
        return redirect()->route(
            'reports.create',
            array_filter([
                'construction_id' => $this->constructionId ?: null,
            ]),
        );
    } catch (\Throwable $e) {
        $this->error = '検索でエラーが発生しました。';
        logger()->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
    } finally {
        $this->isSearching = false;
        $this->hasSearched = true;
    }
};
?>

<section class="max-w-5xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">作業日報検索</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">ダッシュボードへ戻る</a>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">工事ID</label>
                <input type="text" wire:model.debounce.400ms="constructionId"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    placeholder="例: C-2025-001" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">現場名</label>
                <input type="text" wire:model.debounce.400ms="site"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    placeholder="部分一致可" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">件名</label>
                <input type="text" wire:model.debounce.400ms="subject"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    placeholder="部分一致可" />
            </div>
        </div>

        @php($hasInput = filled($constructionId) || filled($site) || filled($subject))
        <div class="flex justify-end">
            <button wire:click="search" wire:loading.attr="disabled" @disabled(!$hasInput)
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                <svg wire:loading class="-ml-1 mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                検索
            </button>
        </div>

        @if ($error)
            <div class="text-sm text-red-600">{{ $error }}</div>
        @endif
    </div>

    @if ($hasSearched)
        <div class="mt-6">
            <div
                class="overflow-x-auto bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">工事ID
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">現場名
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">件名</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($results as $row)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/60">
                                <td class="px-4 py-3 whitespace-nowrap">{{ $row['construction_id'] }}</td>
                                <td class="px-4 py-3">{{ $row['site_name'] }}</td>
                                <td class="px-4 py-3">{{ $row['subject'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    検索結果がありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</section>
