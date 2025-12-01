<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verse extends Model
{
    use HasFactory;

    protected $fillable = [
        'surah_number',
        'verse_number',
        'arabic_text',
        'transliteration',
        'translation_english',
        'translation_bengali',
        'juz',
        'page',
        'revelation_type',
    ];

    protected $casts = [
        'surah_number' => 'integer',
        'verse_number' => 'integer',
        'juz' => 'integer',
        'page' => 'integer',
    ];

    /**
     * Get the surah this verse belongs to
     */
    public function surah()
    {
        return $this->belongsTo(Surah::class, 'surah_number', 'surah_number');
    }

    /**
     * Get users who have progress on this verse
     */
    public function userProgress()
    {
        return $this->hasMany(UserVerseProgress::class);
    }

    /**
     * Get the verse reference (e.g., "2:255" for Ayat al-Kursi)
     */
    public function getVerseReferenceAttribute(): string
    {
        return "{$this->surah_number}:{$this->verse_number}";
    }
}
