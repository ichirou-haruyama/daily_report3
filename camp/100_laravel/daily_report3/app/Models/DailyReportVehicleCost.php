<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportVehicleCost extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'daily_report_id',
        'vehicle_id',
        'outbound_start',
        'outbound_end',
        'from_location',
        'to_location',
        'distance_km',
        'toll_not_used',
        'toll_entry',
        'toll_exit',
        'toll_amount',
        'return_start',
        'return_end',
        'return_from_location',
        'return_to_location',
        'return_distance_km',
        'return_toll_not_used',
        'return_toll_entry',
        'return_toll_exit',
        'return_toll_amount',
    ];

    /**
     * @return BelongsTo<DailyReport, DailyReportVehicleCost>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'daily_report_id');
    }

    /**
     * @return BelongsTo<Vehicle, DailyReportVehicleCost>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
