<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'status',
        'due_at',
        'paid_at',
        'line_items',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'line_items' => 'array',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $invoice) {
            if (empty($invoice->number)) {
                $year = now()->format('Y');
                $last = static::where('number', 'like', "INV-{$year}-%")->orderByDesc('id')->value('number');
                $seq = $last ? ((int) substr($last, -5)) + 1 : 1;
                $invoice->number = sprintf('INV-%s-%05d', $year, $seq);
            }
        });
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
