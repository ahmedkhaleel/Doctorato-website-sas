<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'user_name', 'user_role',
        'action', 'subject_type', 'subject_id', 'subject_label',
        'description', 'changes', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Fluent logger: log an action performed by the current user.
     *
     * Example:
     *   ActivityLog::record('created', $subscription, "Created subscription for {$sub->customer_name}");
     */
    public static function record(string $action, ?Model $subject = null, ?string $description = null, array $changes = null): self
    {
        $request = request();
        $user = auth()->user();

        return static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_role' => $user?->role,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'subject_label' => $subject?->activityLabel ?? $subject?->name ?? null,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->header('User-Agent'),
        ]);
    }
}
