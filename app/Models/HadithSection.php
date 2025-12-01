<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'section_number',
        'section_name_english',
        'section_name_arabic'
    ];

    protected $casts = [
        'section_number' => 'decimal:1'
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(HadithChapter::class, 'chapter_id');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class, 'section_id');
    }
}