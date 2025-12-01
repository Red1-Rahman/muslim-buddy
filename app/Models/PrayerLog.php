<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prayer_date',
        'prayer_name',
        'is_completed',
        'completed_at',
        'on_time',
        'in_congregation',
        'at_mosque',
        'points_earned',
    ];

    protected $casts = [
        'prayer_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'on_time' => 'boolean',
        'in_congregation' => 'boolean',
        'at_mosque' => 'boolean',
        'points_earned' => 'integer',
    ];

    /**
     * Get the user that owns this prayer log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark prayer as completed
     */
    public function markAsCompleted(bool $onTime = false, bool $inCongregation = false, bool $atMosque = false): void
    {
        $points = $this->calculatePoints($onTime, $inCongregation, $atMosque);

        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'on_time' => $onTime,
            'in_congregation' => $inCongregation,
            'at_mosque' => $atMosque,
            'points_earned' => $points,
        ]);

        // Award points to user
        $this->user->increment('total_points', $points);

        // Update prayer streak
        $this->user->updatePrayerStreak();
    }

    /**
     * Calculate points based on prayer quality
     */
    private function calculatePoints(bool $onTime, bool $inCongregation, bool $atMosque): int
    {
        $points = 10; // Base points for completing prayer

        if ($onTime) {
            $points += 5; // Bonus for praying on time
        }

        if ($inCongregation) {
            $points += 10; // Bonus for congregation
        }

        if ($atMosque) {
            $points += 5; // Bonus for praying at mosque
        }

        return $points;
    }

    /**
     * Check if prayer is on time based on prayer times
     */
    public static function isOnTime(string $prayerName, \DateTime $completedAt, array $prayerTimes): bool
    {
        // Implementation would check if completed within prayer time window
        // This is a simplified version
        return true;
    }
}
