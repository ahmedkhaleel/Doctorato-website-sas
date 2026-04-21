<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block admin routes from users who don't have the required permission.
 *
 * The frontend hides nav items using hasPerm() in AdminLayout.vue, but
 * those checks are cosmetic — without this middleware a logged-in user
 * with `dashboard.view` could still POST to /admin/plans/1 and delete
 * a plan. Apply to any destructive/admin-only route group.
 *
 * Usage:
 *   ->middleware('admin.perm:plans.manage')
 *   ->middleware('admin.perm:subscriptions.manage')
 *
 * super_admin + admin roles always pass (see User::hasPermission()).
 */
class AdminPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasPermission($permission)) {
            abort(403, 'ليس لديك صلاحية لهذا الإجراء');
        }
        return $next($request);
    }
}
