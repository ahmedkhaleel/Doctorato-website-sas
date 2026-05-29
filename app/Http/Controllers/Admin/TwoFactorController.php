<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/**
 * Self-service 2FA enrolment + management for the signed-in admin.
 *
 * Routes (all under auth + admin prefix):
 *   GET  /admin/2fa             — status page (enabled? recovery left?)
 *   POST /admin/2fa/setup       — generate a pending secret + QR URI
 *   POST /admin/2fa/confirm     — verify first code, finalize enrolment
 *   POST /admin/2fa/disable     — turn 2FA off (requires current password)
 *   POST /admin/2fa/regenerate-recovery — burn old codes, mint new ones
 *
 * Secret + recovery codes are encrypted at rest. The "pending" secret
 * during setup lives in the session, NOT the DB, so a half-finished
 * enrolment doesn't lock anyone out.
 */
class TwoFactorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Admin/TwoFactorSettings', [
            'enabled' => $user->two_factor_confirmed_at !== null,
            'confirmedAt' => $user->two_factor_confirmed_at,
            'recoveryCodesRemaining' => count(app(TwoFactorService::class)->decryptRecoveryCodes($user)),
        ]);
    }

    public function setup(Request $request, TwoFactorService $twoFactor)
    {
        $user = Auth::user();

        // If already enabled, force the user to disable first so they
        // can't accidentally orphan their authenticator app entry.
        if ($user->two_factor_confirmed_at !== null) {
            return back()->withErrors(['code' => '2FA مفعّل بالفعل — عطّله أولاً قبل إعادة الإعداد.']);
        }

        $secret = $twoFactor->generateSecret();
        $request->session()->put('2fa.pending_secret', Crypt::encryptString($secret));

        return back()->with('twoFactorSetup', [
            'secret' => $secret,
            'uri' => $twoFactor->provisioningUri($secret, $user->email),
        ]);
    }

    public function confirm(Request $request, TwoFactorService $twoFactor)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $encryptedPending = $request->session()->get('2fa.pending_secret');
        if (!$encryptedPending) {
            return back()->withErrors(['code' => 'ابدأ الإعداد من جديد.']);
        }
        $secret = Crypt::decryptString($encryptedPending);

        if (!$twoFactor->verify($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'الكود غير صحيح، حاول مرة أخرى.']);
        }

        $user = Auth::user();
        $recoveryCodes = $twoFactor->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('2fa.pending_secret');
        Log::info('auth.2fa_enabled', ['user_id' => $user->id]);

        return back()->with('twoFactorRecovery', $recoveryCodes);
    }

    public function disable(Request $request, TwoFactorService $twoFactor)
    {
        $request->validate(['password' => 'required|string']);

        $user = Auth::user();
        if (!\Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'كلمة المرور غير صحيحة.']);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        Log::warning('auth.2fa_disabled', ['user_id' => $user->id, 'ip' => $request->ip()]);
        return back();
    }

    public function regenerateRecoveryCodes(Request $request, TwoFactorService $twoFactor)
    {
        $user = Auth::user();
        if ($user->two_factor_confirmed_at === null) {
            return back()->withErrors(['code' => '2FA غير مفعّل.']);
        }

        $codes = $twoFactor->generateRecoveryCodes();
        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($codes)),
        ])->save();

        Log::info('auth.2fa_recovery_regenerated', ['user_id' => $user->id]);
        return back()->with('twoFactorRecovery', $codes);
    }
}
