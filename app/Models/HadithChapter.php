<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'chapter_number',
        'chapter_name_english',
        'chapter_name_arabic'
    ];

    protected $casts = [
        'chapter_number' => 'decimal:1'
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(HadithCollection::class, 'collection_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(HadithSection::class, 'chapter_id');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class, 'chapter_id');
    }
}