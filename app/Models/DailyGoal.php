<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'goal_date',
        'target_verses',
        'verses_completed',
        'all_prayers_completed',
    ];

    protected $casts = [
        'goal_date' => 'date',
        'target_verses' => 'integer',
        'verses_completed' => 'integer',
        'all_prayers_completed' => 'boolean',
    ];

    /**
     * Get the user that owns this goal
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if daily goal is achieved
     */
    public function isAchieved(): bool
    {
        return $this->verses_completed >= $this->target_verses && $this->all_prayers_completed;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_verses == 0) {
            return 0;
        }
        return min(100, round(($this->verses_completed / $this->target_verses) * 100, 2));
    }

    /**
     * Update verse progress
     */
    public function updateVerseProgress(): void
    {
        $count = UserVerseProgress::where('user_id', $this->user_id)
            ->whereDate('read_at', $this->goal_date)
            ->where('is_read', true)
            ->count();

        $this->update(['verses_completed' => $count]);
    }

    /**
     * Update prayer completion status
     */
    public function updatePrayerStatus(): void
    {
        $completedPrayers = PrayerLog::where('user_id', $this->user_id)
            ->where('prayer_date', $this->goal_date)
            ->where('is_completed', true)
            ->count();

        $this->update(['all_prayers_completed' => $completedPrayers >= 5]);
    }
}
