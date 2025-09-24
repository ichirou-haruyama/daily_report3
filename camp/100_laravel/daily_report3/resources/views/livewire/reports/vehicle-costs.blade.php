<?php

use function Livewire\Volt\{layout, title};

layout('components.layouts.app');
title('車両費 入力');
?>

<section class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">車両費 入力（プレースホルダー）</h1>
        <a href="{{ route('reports.create') }}" class="text-sm text-blue-600 hover:underline">日報作成に戻る</a>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg p-5 space-y-4">
        <p class="text-gray-700 dark:text-gray-200">ここで車両費入力フォームを実装予定です。</p>
        <div class="flex gap-3">
            <a href="{{ route('reports.other') }}"
                class="inline-flex items-center rounded-md bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">その他へ</a>
            <a href="{{ route('reports.create') }}"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">日報作成に戻る</a>
        </div>
    </div>
</section>
