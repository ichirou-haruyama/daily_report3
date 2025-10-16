<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportVehicleToll extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'vehicle_cost_id',
        'direction',
        'entry_time',
        'entry_ic',
        'exit_time',
        'exit_ic',
        'amount',
        'method',
    ];

    /**
     * @return BelongsTo<DailyReportVehicleCost, DailyReportVehicleToll>
     */
    public function vehicleCost(): BelongsTo
    {
        return $this->belongsTo(DailyReportVehicleCost::class, 'vehicle_cost_id');
    }
}
