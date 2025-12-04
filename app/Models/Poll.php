<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'accepts_responses' => 'boolean',
        'require_email' => 'boolean',
        'auto_submit' => 'boolean',
        'collect_feedback' => 'boolean',
        'hide_branding' => 'boolean',
    ];

    protected $attributes = [
        'accepts_responses' => true,
        'require_email' => false,
        'auto_submit' => false,
        'collect_feedback' => false,
        'hide_branding' => false,
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->orderBy('sort_order');
    }

    public function pollResponses(): HasMany
    {
        return $this->hasMany(PollResponse::class);
    }
}
