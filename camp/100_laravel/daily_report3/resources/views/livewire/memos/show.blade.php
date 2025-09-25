<?php

use App\Models\Memo;
use Livewire\Volt\{state};

/**
 * 作業日報詳細コンポーネント
 * - ルートパラメータの Memo を表示
 */

state(['memo' => fn(Memo $memo) => $memo->load('user')]);
?>

<section class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">作業日報詳細</h1>
        <a href="{{ route('memos.index') }}" class="text-sm text-blue-600 hover:underline">一覧へ戻る</a>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg p-6">
        <div class="mb-2 text-sm text-gray-600 dark:text-gray-400">
            投稿者: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $memo->user->name ?? '不明' }}</span>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $memo->title }}</h2>
        <div class="mt-4 whitespace-pre-wrap text-gray-800 dark:text-gray-200">{{ $memo->body }}</div>
        <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">作成日:
            {{ optional($memo->created_at)->format('Y-m-d H:i') }}</div>
    </div>
</section>
