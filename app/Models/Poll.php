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

    protected static function boot()
    {
        parent::boot();
    }

    protected $casts = [
        'accepts_responses' => 'boolean',
        'require_email' => 'boolean',
        'auto_submit' => 'boolean',
        'collect_feedback' => 'boolean',
        'hide_branding' => 'boolean',
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

    public function submissionAction(): string
    {
        return $this->redirect_url ? 'redirect' : 'message';
    }

    public function shouldRedirect(): bool
    {
        return $this->submissionAction() === 'redirect';
    }

    public function delete()
    {
        $this->pollResponses()->delete();
        $this->answers()->delete();

        parent::delete();
    }
}
