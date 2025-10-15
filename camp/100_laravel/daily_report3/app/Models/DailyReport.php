<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DailyReport extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'report_date',
        'construction_id',
        'work_summary',
        'weather',
        'start_time',
        'end_time',
        'total_hours',
        'status',
    ];

    /**
     * @return BelongsTo<User, DailyReport>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne<DailyReportVehicleCost>
     */
    public function vehicleCost(): HasOne
    {
        return $this->hasOne(DailyReportVehicleCost::class);
    }
}
