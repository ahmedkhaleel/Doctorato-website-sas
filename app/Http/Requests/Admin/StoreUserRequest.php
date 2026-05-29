<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin → Users → Create. Rules pulled out of the controller so:
 *   - The role + permission whitelist lives next to where it's used
 *     (the User model statics) and gates malicious payloads BEFORE
 *     the controller is even invoked.
 *   - The `users.manage` permission acts as a second gate at the
 *     authorize() layer — defence in depth in case the route
 *     middleware is ever misconfigured.
 *   - Passwords use 'confirmed' so the UI can require a second
 *     entry without the controller having to coordinate that.
 */
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('users.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(array_keys(User::roles()))],
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in(array_keys(User::availablePermissions()))],
            'is_active' => 'boolean',
        ];
    }
}
