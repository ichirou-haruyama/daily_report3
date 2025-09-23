<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * memos テーブルの初期データを投入するシーダー
 */
class MemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存ユーザーがなければ作成
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $samples = [
            ['title' => 'PHP', 'body' => 'PHPは、Hypertext Preprocessorの略です。'],
            ['title' => 'HTML', 'body' => 'HTMLは、Hypertext Markup Languageの略です。'],
            ['title' => 'CSS', 'body' => "CSSは、\nCascading Style Sheets\nの略です。"],
            ['title' => '混在', 'body' => "Test123 てすとアイウエオｱｲｳｴｵ\n漢字！ＡＢＣ ａｂｃ １２３   😊✨"],
        ];

        foreach ($samples as $sample) {
            Memo::updateOrCreate(
                ['user_id' => $user->id, 'title' => $sample['title']],
                ['body' => $sample['body']]
            );
        }
    }
}
