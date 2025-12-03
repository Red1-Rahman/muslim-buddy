<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerseProgress extends Model
{
    use HasFactory;

    protected $table = 'user_verse_progress';

    protected $fillable = [
        'user_id',
        'verse_id',
        'is_read',
        'is_understood',
        'is_memorized',
        'read_at',
        'understood_at',
        'memorized_at',
        'review_count',
        'last_reviewed_at',
        'next_review_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_understood' => 'boolean',
        'is_memorized' => 'boolean',
        'read_at' => 'datetime',
        'understood_at' => 'datetime',
        'memorized_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
        'next_review_at' => 'datetime',
        'review_count' => 'integer',
    ];

    /**
     * Get the user that owns this progress
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the verse this progress is for
     */
    public function verse()
    {
        return $this->belongsTo(Verse::class);
    }

    /**
     * Mark verse as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        // Award points
        $this->user->increment('total_points', 1);
    }

    /**
     * Mark verse as understood
     */
    public function markAsUnderstood(): void
    {
        $this->update([
            'is_understood' => true,
            'understood_at' => now(),
        ]);

        // Award points
        $this->user->increment('total_points', 2);
    }

    /**
     * Mark verse as memorized
     */
    public function markAsMemorized(): void
    {
        $this->update([
            'is_memorized' => true,
            'memorized_at' => now(),
            'next_review_at' => now()->addDay(), // First review after 1 day
        ]);

        // Award points
        $this->user->increment('total_points', 5);
    }

    /**
     * Schedule next review using spaced repetition
     */
    public function scheduleNextReview($difficulty = 'easy'): void
    {
        $intervals = [1, 3, 7, 14, 30, 60]; // Days
        $reviewCount = min($this->review_count, count($intervals) - 1);
        
        // Adjust interval based on difficulty
        switch ($difficulty) {
            case 'hard':
                // Reset to 1 day if hard
                $nextInterval = 1;
                break;
            case 'medium':
                // Use half the normal interval (minimum 1 day)
                $nextInterval = max(1, intval($intervals[$reviewCount] / 2));
                break;
            case 'easy':
            default:
                // Use normal interval
                $nextInterval = $intervals[$reviewCount];
                break;
        }

        $this->update([
            'review_count' => $difficulty === 'hard' ? $this->review_count : $this->review_count + 1,
            'last_reviewed_at' => now(),
            'next_review_at' => now()->addDays($nextInterval),
        ]);
    }
}
