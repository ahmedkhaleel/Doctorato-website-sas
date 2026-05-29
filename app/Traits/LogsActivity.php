<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Auto-log create / update / delete on models that need an audit trail.
 *
 * Usage on a model:
 *   use App\Traits\LogsActivity;
 *   class Subscription extends Model {
 *       use LogsActivity;
 *       protected $activityLogSubject = 'Subscription'; // optional override
 *   }
 *
 * What it captures:
 *   - user_id, user_name, user_role (Auth::user() at the time)
 *   - action: 'created' | 'updated' | 'deleted'
 *   - subject_type + subject_id (morphTo so an admin view can list
 *     every change to a single Subscription / Invoice / etc.)
 *   - changes: { field: [old, new] } for updates, full attributes for
 *     create/delete
 *   - IP + user agent from the current request
 *
 * Why a trait instead of a Spatie package:
 *   - One file, no extra composer dep
 *   - We already have the ActivityLog table from earlier work
 *   - We can grow it into Spatie later if we need polymorphic
 *     subjects or batch operations — the schema is compatible.
 */
trait LogsActivity
{
    /**
     * Fields we never want stamped to the audit log. Override on the
     * model if it has hashed/private columns we don't want spread
     * across log rows that admins read.
     */
    protected array $activityLogExclude = ['password', 'remember_token', 'api_token'];

    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            self::writeActivity($model, 'created', $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $changes = [];
            foreach ($model->getChanges() as $field => $newValue) {
                if (in_array($field, $model->activityLogExclude ?? [], true)) {
                    continue;
                }
                $changes[$field] = [
                    'from' => $model->getOriginal($field),
                    'to' => $newValue,
                ];
            }
            // Skip pure-timestamp updates (touch) — they're noise.
            $meaningfulChanges = array_diff_key($changes, array_flip(['updated_at']));
            if (empty($meaningfulChanges)) {
                return;
            }
            self::writeActivity($model, 'updated', $changes);
        });

        static::deleted(function (Model $model) {
            self::writeActivity($model, 'deleted', $model->getAttributes());
        });
    }

    protected static function writeActivity(Model $model, string $action, array $changes): void
    {
        $user = Auth::user();
        $request = request();

        try {
            ActivityLog::create([
                'user_id'       => $user?->id,
                'user_name'     => $user?->name ?? 'system',
                'user_role'     => $user?->role ?? 'system',
                'action'        => $action,
                'subject_type'  => $model->activityLogSubject ?? class_basename($model),
                'subject_id'    => $model->getKey(),
                'subject_label' => $model->getActivityLogLabel(),
                'description'  => null,
                'changes'      => $changes,
                'ip_address'   => $request?->ip(),
                'user_agent'   => $request?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Logging is best-effort — never break the model save
            // because the audit table had a hiccup. Log to the
            // standard error log so an admin can spot the failure
            // in the daily log review.
            \Illuminate\Support\Facades\Log::warning('ActivityLog write failed', [
                'error' => $e->getMessage(),
                'model' => class_basename($model),
                'id' => $model->getKey(),
            ]);
        }
    }

    /**
     * Human-readable label for the activity-log subject column.
     * Override on the model to return something more useful than
     * the primary key (e.g. clinic name, invoice number).
     */
    public function getActivityLogLabel(): ?string
    {
        // Sensible default: name field if present, otherwise the id.
        foreach (['name', 'clinic_name', 'full_name', 'title', 'invoice_number'] as $candidate) {
            if (!empty($this->{$candidate})) {
                return (string) $this->{$candidate};
            }
        }
        return null;
    }
}
