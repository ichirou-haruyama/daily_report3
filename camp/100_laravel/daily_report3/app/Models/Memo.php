<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * メモモデル
 *
 * @property int $id 主キー
 * @property int $user_id ユーザーID
 * @property string $title タイトル
 * @property string $body 本文
 * @property \Carbon\CarbonImmutable|null $created_at 作成日時
 * @property \Carbon\CarbonImmutable|null $updated_at 更新日時
 */
class Memo extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];

    /**
     * ユーザーへのリレーション
     *
     * @return BelongsTo<User, Memo>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
