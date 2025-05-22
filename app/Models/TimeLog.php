<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLog extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'start_time',
        'end_time',
        'description',
        'hours',
        'is_billable',
        'tags',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hours' => 'decimal:2',
        'is_billable' => 'boolean',
    ];

    /**
     * Get the project that owns the time log.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Calculate hours based on start and end time when setting end_time.
     */
    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = $value;

        if ($value && isset($this->attributes['start_time'])) {
            $startTime = new Carbon($this->attributes['start_time']);
            $endTime = new Carbon($value);

            // Calculate hours as decimal
            $this->attributes['hours'] = $startTime->diffInMinutes($endTime) / 60;
        }
    }

    /**
     * Convert tags string to array.
     */
    public function getTagsArrayAttribute()
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }

    /**
     * Set tags from array.
     */
    public function setTagsArrayAttribute(array $tags)
    {
        $this->attributes['tags'] = implode(',', $tags);
    }
}
