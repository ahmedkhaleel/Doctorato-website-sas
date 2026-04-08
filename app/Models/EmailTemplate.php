<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'subject_ar', 'subject_en', 'body_ar', 'body_en',
        'label_ar', 'label_en', 'description', 'variables', 'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /** Fetch a template by key. */
    public static function for(string $key): ?self
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }

    /**
     * Render the template with the given data and locale.
     * Returns ['subject' => ..., 'body' => ...] or null when the template
     * is not found / not active.
     */
    public static function render(string $key, array $data = [], string $locale = 'ar'): ?array
    {
        $tpl = static::for($key);
        if (!$tpl) return null;

        $subject = $tpl->{"subject_{$locale}"} ?: $tpl->subject_ar;
        $body = $tpl->{"body_{$locale}"} ?: $tpl->body_ar;

        foreach ($data as $var => $value) {
            $subject = str_replace('{{' . $var . '}}', (string) $value, $subject);
            $body = str_replace('{{' . $var . '}}', (string) $value, $body);
        }

        return compact('subject', 'body');
    }
}
