<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetricSnapshot extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'snapshot_date',
        'mrr_sar', 'arr_sar', 'active_subs', 'paused_subs',
        'past_due_subs', 'arpu_sar', 'churn_30d_pct',
        'new_subs', 'cancelled_subs', 'captured_at',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'captured_at' => 'datetime',
        'mrr_sar' => 'decimal:2',
        'arr_sar' => 'decimal:2',
        'arpu_sar' => 'decimal:2',
        'churn_30d_pct' => 'decimal:2',
    ];
}
