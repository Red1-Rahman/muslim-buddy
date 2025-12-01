<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hadith extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'chapter_id',
        'section_id',
        'hadith_number',
        'english_hadith',
        'english_isnad',
        'english_matn',
        'arabic_hadith',
        'arabic_isnad',
        'arabic_matn',
        'arabic_comment',
        'english_grade',
        'arabic_grade'
    ];

    protected $casts = [
        'hadith_number' => 'decimal:1'
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(HadithCollection::class, 'collection_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(HadithChapter::class, 'chapter_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(HadithSection::class, 'section_id');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserHadithProgress::class, 'hadith_id');
    }

    public function getGradeColorAttribute(): string
    {
        return match(strtolower($this->english_grade)) {
            'sahih-authentic', 'sahih - authentic' => 'text-green-600',
            'hasan' => 'text-yellow-600',
            'daif', 'weak' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    public function getFormattedNumberAttribute(): string
    {
        return number_format($this->hadith_number, 0);
    }
}