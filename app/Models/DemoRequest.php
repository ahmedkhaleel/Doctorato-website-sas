<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_name',
        'full_name',
        'email',
        'phone',
        'country_code',
        'country',
        'doctors_count',
        'specialty',
        'interested_modules',
        'referral_source',
        'notes',
        'status',
        'admin_notes',
        'contacted_at',
    ];

    protected $casts = [
        'interested_modules' => 'array',
        'contacted_at' => 'datetime',
    ];
}
