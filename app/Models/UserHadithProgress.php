<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHadithProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hadith_id',
        'status',
        'read_at',
        'memorized_at',
        'notes'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'memorized_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }

    public function markAsRead(): void
    {
        $this->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }

    public function markAsMemorized(): void
    {
        $this->update([
            'status' => 'memorized',
            'memorized_at' => now()
        ]);
    }
}