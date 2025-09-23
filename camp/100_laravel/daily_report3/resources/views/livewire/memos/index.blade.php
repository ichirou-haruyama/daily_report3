<?php

use App\Models\Memo;
use Livewire\Volt\{with};

/**
 * 作業日報一覧コンポーネント（読み取り専用）
 * - Eager Loadで投稿者(User)を読み込み
 * - シンプルなページネーション
 */
with(
    fn() => [
        'memos' => Memo::query()->with('user')->latest('id')->paginate(10),
    ],
);
?>

<section class="max-w-5xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">作業日報一覧</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">ダッシュボードへ戻る</a>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">タイトル</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">投稿者</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">作成日</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($memos as $memo)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/60">
                        <td class="px-4 py-3 whitespace-normal">
                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $memo->title }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-700 dark:text-gray-300">{{ $memo->user->name ?? '不明' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="text-gray-600 dark:text-gray-400">{{ optional($memo->created_at)->format('Y-m-d H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('memos.show', ['memo' => $memo]) }}"
                                class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">データがありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $memos->links() }}
    </div>
</section>
