<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'latitude',
        'longitude',
        'location_name',
        'calculation_method',
        'madhab',
        'timezone',
        'bio',
        'avatar',
        'total_points',
        'prayer_streak',
        'last_prayer_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_prayer_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user's verse progress records
     */
    public function verseProgress()
    {
        return $this->hasMany(UserVerseProgress::class);
    }

    /**
     * Get the user's prayer logs
     */
    public function prayerLogs()
    {
        return $this->hasMany(PrayerLog::class);
    }

    /**
     * Get the user's daily goals
     */
    public function dailyGoals()
    {
        return $this->hasMany(DailyGoal::class);
    }

    /**
     * Get verses the user has read
     */
    public function readVerses()
    {
        return $this->belongsToMany(Verse::class, 'user_verse_progress')
            ->wherePivot('is_read', true);
    }

    /**
     * Get verses the user has understood
     */
    public function understoodVerses()
    {
        return $this->belongsToMany(Verse::class, 'user_verse_progress')
            ->wherePivot('is_understood', true);
    }

    /**
     * Get verses the user has memorized
     */
    public function memorizedVerses()
    {
        return $this->belongsToMany(Verse::class, 'user_verse_progress')
            ->wherePivot('is_memorized', true);
    }

    /**
     * Calculate total Quran progress percentage
     */
    public function getQuranProgressPercentageAttribute(): float
    {
        $totalVerses = 6236; // Total verses in Quran
        $readCount = $this->verseProgress()->where('is_read', true)->count();
        return round(($readCount / $totalVerses) * 100, 2);
    }

    /**
     * Calculate memorization progress percentage
     */
    public function getMemorizationProgressPercentageAttribute(): float
    {
        $totalVerses = 6236;
        $memorizedCount = $this->verseProgress()->where('is_memorized', true)->count();
        return round(($memorizedCount / $totalVerses) * 100, 2);
    }

    /**
     * Get today's prayer completion status
     */
    public function getTodayPrayerStatusAttribute(): array
    {
        $today = now()->toDateString();
        $prayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
        $status = [];
        
        foreach ($prayers as $prayer) {
            $log = $this->prayerLogs()
                ->where('prayer_date', $today)
                ->where('prayer_name', $prayer)
                ->first();
            
            $status[$prayer] = [
                'completed' => $log ? $log->is_completed : false,
                'on_time' => $log ? $log->on_time : false,
                'points' => $log ? $log->points_earned : 0
            ];
        }
        
        return $status;
    }

    /**
     * Update prayer streak
     */
    public function updatePrayerStreak(): void
    {
        $today = now()->toDateString();
        
        // Check if all 5 prayers are completed today
        $todayCompleted = $this->prayerLogs()
            ->where('prayer_date', $today)
            ->where('is_completed', true)
            ->count();
        
        if ($todayCompleted >= 5) {
            if ($this->last_prayer_date && $this->last_prayer_date->isYesterday()) {
                $this->increment('prayer_streak');
            } else {
                $this->prayer_streak = 1;
            }
            $this->last_prayer_date = $today;
            $this->save();
        }
    }
}
