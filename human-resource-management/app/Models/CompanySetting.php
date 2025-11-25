<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'late_threshold',
        'regular_hours',
        'burnout_threshold',
        'work_start_time',
        'work_end_time'
    ];

    protected $casts = [
        'regular_hours' => 'decimal:2',
        'burnout_threshold' => 'integer',
        'late_threshold' => 'datetime:H:i:s',
        'work_start_time' => 'datetime:H:i:s',
        'work_end_time' => 'datetime:H:i:s',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Get default settings if none exist
    public static function getDefaultSettings()
    {
        return new self([
            'late_threshold' => '09:15:00',
            'regular_hours' => 8.00,
            'burnout_threshold' => 48, // Changed from 40 to 48
            'work_start_time' => '09:00:00',
            'work_end_time' => '17:00:00',
        ]);
    }

    // Accessor for formatted late threshold
    public function getLateThresholdFormattedAttribute()
    {
        return $this->late_threshold ? Carbon::parse($this->late_threshold)->format('h:i A') : '09:15 AM';
    }
}