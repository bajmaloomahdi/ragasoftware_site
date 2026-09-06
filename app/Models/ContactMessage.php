<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    public const STATUSES = ['new', 'read', 'answered', 'closed'];

    protected $fillable = [
        'name', 'phone', 'email', 'company', 'subject', 'message', 'status',
        'ip_address', 'user_agent', 'source_path', 'replied_at', 'replied_by', 'admin_notes',
    ];

    protected $casts = ['replied_at' => 'datetime'];

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function scopeStatus(Builder $q, ?string $status): void
    {
        if ($status) {
            $q->where('status', $status);
        }
    }
}
