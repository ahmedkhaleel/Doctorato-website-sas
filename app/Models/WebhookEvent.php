<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEvent extends Model
{
    /** Status machine. */
    public const STATUS_RECEIVED = 'received';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REPLAYED = 'replayed';

    public $timestamps = false;

    protected $fillable = [
        'source', 'event_type', 'gateway_id', 'order_id',
        'hmac_valid', 'status', 'payload', 'response_code',
        'response_body', 'error', 'replayed_from_id', 'ip',
        'received_at', 'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'hmac_valid' => 'boolean',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function origin(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replayed_from_id');
    }
}
