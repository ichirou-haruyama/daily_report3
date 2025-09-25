<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Google Sheets から工事台帳を読み出し、検索するサービス。
 * - .env でスプレッドシートIDと読み取りレンジを差し替え可能
 * - Simple cache で短時間キャッシュ（APIコール削減）
 */
class SheetSearchService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(?string $constructionId, ?string $siteName, ?string $subject): array
    {
        $rows = $this->fetchSheetRows();
        if ($rows === [] || !isset($rows[0]) || !is_array($rows[0])) {
            return [];
        }

        // ヘッダーあり/なしを自動判定
        $headers = array_map(static fn($h) => trim((string) $h), $rows[0]);
        $headerIdx = [
            'construction_id' => $this->findHeaderIndex($headers, ['工事ID', 'construction_id', 'ID']),
            'site_name' => $this->findHeaderIndex($headers, ['現場名', 'site_name', '元請会社と現場名', '元請 現場名']),
            'subject' => $this->findHeaderIndex($headers, ['件名', 'subject', '工事名', 'タイトル']),
        ];
        $hasHeader = ($headerIdx['construction_id'] >= 0) || ($headerIdx['site_name'] >= 0) || ($headerIdx['subject'] >= 0);

        // 列インデックスを決定（ヘッダーが無ければ A:0, B:1, C:2）
        $col = [
            'construction_id' => $hasHeader ? ($headerIdx['construction_id'] >= 0 ? $headerIdx['construction_id'] : 0) : 0,
            'site_name' => $hasHeader ? ($headerIdx['site_name'] >= 0 ? $headerIdx['site_name'] : 1) : 1,
            'subject' => $hasHeader ? ($headerIdx['subject'] >= 0 ? $headerIdx['subject'] : 2) : 2,
        ];

        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        $results = [];
        $qId = $this->normalizeString((string) ($constructionId ?? ''));
        $qSite = $this->normalizeString((string) ($siteName ?? ''));
        $qSubj = $this->normalizeString((string) ($subject ?? ''));

        foreach ($dataRows as $row) {
            $row = is_array($row) ? $row : [];
            $record = [
                'construction_id' => (string) Arr::get($row, $col['construction_id'], ''),
                // B列は「元請会社と現場名」の複合をそのまま site_name として扱う
                'site_name' => (string) Arr::get($row, $col['site_name'], ''),
                'subject' => (string) Arr::get($row, $col['subject'], ''),
            ];

            $rid = $this->normalizeString($record['construction_id']);
            $rsite = $this->normalizeString($record['site_name']);
            $rsubj = $this->normalizeString($record['subject']);

            if ($qId !== '' && !($rid === $qId || Str::contains($rid, $qId))) {
                continue;
            }
            if ($qSite !== '' && !Str::contains($rsite, $qSite)) {
                continue;
            }
            if ($qSubj !== '' && !Str::contains($rsubj, $qSubj)) {
                continue;
            }

            $results[] = $record;
            if (count($results) >= 100) {
                break;
            }
        }

        return $results;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function fetchSheetRows(): array
    {
        $cacheKey = 'sheets:constructions:' . md5((string) Config::get('services.google_sheets.spreadsheet_id') . '|' . (string) Config::get('services.google_sheets.range'));

        return Cache::remember($cacheKey, now()->addMinutes(5), function (): array {
            $clientClass = '\\Google\\Client';
            $sheetsClass = '\\Google\\Service\\Sheets';
            if (!class_exists($clientClass) || !class_exists($sheetsClass)) {
                throw new \RuntimeException('Google API client is not installed. Run: composer require google/apiclient google/apiclient-services');
            }

            $client = new $clientClass();
            $client->setApplicationName(Config::get('app.name', 'Laravel'));
            $client->setScopes((array) Config::get('services.google_sheets.scopes', []));

            $jsonPath = (string) Config::get('services.google_sheets.service_account_json_path');
            if (!is_readable($jsonPath)) {
                throw new \RuntimeException('Google Service Account JSON not found: ' . $jsonPath);
            }
            $client->setAuthConfig($jsonPath);

            $service = new $sheetsClass($client);
            $spreadsheetId = (string) Config::get('services.google_sheets.spreadsheet_id');
            $range = (string) Config::get('services.google_sheets.range', '工事内容管理!A:C');

            if ($spreadsheetId === '') {
                return [];
            }

            $resp = $service->spreadsheets_values->get($spreadsheetId, $range);
            /** @var array<int, array<int, string>> $values */
            $values = $resp->getValues() ?? [];
            return $values;
        });
    }

    /**
     * @param array<int, string> $headers
     */
    private function findHeaderIndex(array $headers, array $candidates): int
    {
        foreach ($candidates as $name) {
            $i = array_search($name, $headers, true);
            if ($i !== false) {
                return (int) $i;
            }
        }
        // 見つからなければ -1 を返す（ヘッダー未検出）
        return -1;
    }

    private function normalizeString(string $value): string
    {
        $v = trim($value);
        if ($v === '') {
            return '';
        }
        // 全角→半角（英数・スペース・記号の基本範囲）
        if (function_exists('mb_convert_kana')) {
            $v = mb_convert_kana($v, 'asKV');
        }
        return $v;
    }
}
