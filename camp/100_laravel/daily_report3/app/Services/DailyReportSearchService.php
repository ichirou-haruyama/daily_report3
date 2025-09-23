<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 作業日報検索サービス
 * - 自分の作業日報のみを対象
 * - 条件: 工事ID(完全一致)、現場名/件名(部分一致)
 * - シートキャッシュ(sheets_constructions_cache)と construction_id で結合
 */
class DailyReportSearchService
{
    /**
     * @param string|null $constructionId 工事ID
     * @param string|null $siteName 現場名(部分一致)
     * @param string|null $subject 件名(部分一致)
     * @return array<int, array<string, mixed>>
     */
    public function search(?string $constructionId, ?string $siteName, ?string $subject): array
    {
        $userId = Auth::id();

        $query = DB::table('daily_reports as dr')
            ->leftJoin('sheets_constructions_cache as s', 's.construction_id', '=', 'dr.construction_id')
            ->select([
                'dr.id',
                'dr.user_id',
                'dr.report_date',
                'dr.construction_id',
                'dr.work_summary',
                'dr.status',
                DB::raw('COALESCE(s.site_name, "") as site_name'),
                DB::raw('COALESCE(s.subject, "") as subject'),
            ])
            ->where('dr.user_id', '=', $userId)
            ->when(
                filled($constructionId),
                fn($q) => $q->where('dr.construction_id', '=', $constructionId)
            )
            ->when(
                filled($siteName),
                fn($q) => $q->where('s.site_name', 'like', '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $siteName) . '%')
            )
            ->when(
                filled($subject),
                fn($q) => $q->where('s.subject', 'like', '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $subject) . '%')
            )
            ->orderByDesc('dr.report_date')
            ->orderByDesc('dr.id')
            ->limit(100);

        return $query->get()->map(fn($row) => (array) $row)->all();
    }
}
