<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * All available roles in the system.
     */
    public static function roles(): array
    {
        return [
            'super_admin' => 'مدير عام',
            'admin' => 'مدير',
            'manager' => 'مدير محتوى',
            'editor' => 'محرر',
            'viewer' => 'مشاهد',
        ];
    }

    /**
     * All available fine-grained permissions.
     */
    public static function availablePermissions(): array
    {
        return [
            'dashboard.view' => 'عرض لوحة التحكم',
            'faqs.manage' => 'إدارة الأسئلة الشائعة',
            'plans.manage' => 'إدارة خطط الأسعار',
            'testimonials.manage' => 'إدارة الشهادات',
            'currencies.manage' => 'إدارة العملات',
            'contacts.manage' => 'إدارة رسائل التواصل',
            'demos.manage' => 'إدارة طلبات العرض',
            'users.manage' => 'إدارة المستخدمين',
            'settings.manage' => 'إدارة الإعدادات',
        ];
    }

    /**
     * Default permissions per role.
     */
    public static function defaultPermissionsForRole(string $role): array
    {
        return match ($role) {
            'super_admin' => array_keys(self::availablePermissions()),
            'admin' => [
                'dashboard.view', 'faqs.manage', 'plans.manage', 'testimonials.manage',
                'currencies.manage', 'contacts.manage', 'demos.manage',
            ],
            'manager' => [
                'dashboard.view', 'faqs.manage', 'testimonials.manage', 'contacts.manage', 'demos.manage',
            ],
            'editor' => [
                'dashboard.view', 'faqs.manage', 'testimonials.manage',
            ],
            'viewer' => ['dashboard.view'],
            default => ['dashboard.view'],
        };
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }
        return in_array($permission, $this->permissions ?? [], true);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
