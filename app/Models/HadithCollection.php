<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_arabic',
        'description',
        'is_verified',
        'accuracy_percentage'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'accuracy_percentage' => 'decimal:2'
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(HadithChapter::class, 'collection_id');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class, 'collection_id');
    }

    public function getAccuracyStatusAttribute(): string
    {
        return $this->is_verified ? 'verified' : 'auto-annotated';
    }

    public function getWarningMessageAttribute(): ?string
    {
        if (!$this->is_verified && $this->accuracy_percentage) {
            return "This collection is auto-annotated with {$this->accuracy_percentage}% accuracy. Please verify important hadiths from scholarly sources.";
        }
        
        return null;
    }
}