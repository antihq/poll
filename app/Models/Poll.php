<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    /** @use HasFactory<\Database\Factories\PollFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'auto_submit' => 'boolean',
        'require_email' => 'boolean',
        'collect_feedback' => 'boolean',
        'hide_branding' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('sort_order');
    }
}
