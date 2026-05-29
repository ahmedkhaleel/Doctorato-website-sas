<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin → Users → Update. Same shape as Store except:
 *   - email unique rule ignores the current row.
 *   - password is nullable (controller skips the update when empty)
 *     so admins can edit name/role without forcing a password reset.
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('users.manage') : (bool) $user;
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');
        $targetId = is_object($targetUser) ? $targetUser->id : $targetUser;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($targetId)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(array_keys(User::roles()))],
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in(array_keys(User::availablePermissions()))],
            'is_active' => 'boolean',
        ];
    }
}
