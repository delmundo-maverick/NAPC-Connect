<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = ['listing_id', 'user_id', 'answers', 'status'];

    protected function casts(): array
    {
        return ['answers' => 'array'];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'submitted' => 'Submitted',
            'reviewed'  => 'Reviewed',
            'accepted'  => 'Accepted',
            'rejected'  => 'Not selected',
            default     => ucfirst((string) $this->status),
        };
    }
}
