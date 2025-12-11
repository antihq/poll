<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollResponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($pollResponse) {
            $pollResponse->poll->team->incrementResponseCount();
        });
    }

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}
