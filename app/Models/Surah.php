<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;

    protected $fillable = [
        'surah_number',
        'name_arabic',
        'name_english',
        'name_transliteration',
        'total_verses',
        'revelation_type',
        'revelation_order',
        'description',
    ];

    protected $casts = [
        'surah_number' => 'integer',
        'total_verses' => 'integer',
        'revelation_order' => 'integer',
    ];

    /**
     * Get all verses in this surah
     */
    public function verses()
    {
        return $this->hasMany(Verse::class, 'surah_number', 'surah_number')
            ->orderBy('verse_number');
    }

    /**
     * Get user progress for this surah
     */
    public function userProgress(int $userId)
    {
        return UserVerseProgress::whereHas('verse', function ($query) {
            $query->where('surah_number', $this->surah_number);
        })->where('user_id', $userId);
    }
}
