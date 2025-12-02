<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function pollResponses(): HasMany
    {
        return $this->hasMany(PollResponse::class);
    }
}
