<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">ダッシュボード</h1>
            <a href="{{ route('reports.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-base font-bold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                ＋ 新規日報を入力
            </a>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-200 dark:ring-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/40">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                入力者</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                入力日時</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                報告日</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                工事ID</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                労務時間</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                距離(km)</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                高速合計(円)</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                概要</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($reports as $report)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $report->created_at->timezone('Asia/Tokyo')->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $report->construction_id }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    @php
                                        $hours =
                                            $report->total_hours ??
                                            (isset($report->start_time, $report->end_time)
                                                ? (strtotime($report->end_time) - strtotime($report->start_time)) / 3600
                                                : null);
                                    @endphp
                                    {{ $hours ? number_format($hours, 2) . ' h' : '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    @php
                                        $distance = 0;
                                        if ($report->vehicleCost) {
                                            $distance += (float) ($report->vehicleCost->distance_km ?? 0);
                                            $distance += (float) ($report->vehicleCost->return_distance_km ?? 0);
                                        }
                                    @endphp
                                    {{ $distance > 0 ? number_format($distance, 1) : '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    @php
                                        $tollSum = 0;
                                        if ($report->vehicleCost) {
                                            $tollSum += (int) ($report->vehicleCost->toll_amount ?? 0);
                                            $tollSum += (int) ($report->vehicleCost->return_toll_amount ?? 0);
                                            foreach ($report->vehicleCost->tolls ?? [] as $toll) {
                                                $tollSum += (int) ($toll->amount ?? 0);
                                            }
                                        }
                                    @endphp
                                    {{ $tollSum > 0 ? number_format($tollSum) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 max-w-[28rem] truncate"
                                    title="{{ $report->work_summary }}">{{ $report->work_summary }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="#" class="text-sm text-indigo-600 hover:underline">詳細</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9"
                                    class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">まだ日報がありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
