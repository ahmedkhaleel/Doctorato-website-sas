<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/**
 * Admin login flow with three guards layered on top of password auth:
 *
 *   1. Credentials check
 *   2. is_active gate — a deactivated user can't log in even with
 *      the right password
 *   3. 2FA challenge if the user finished enrolment
 *
 * Each step writes a structured log line ('auth.login_*') so admins
 * can grep for login events when investigating an incident.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/admin');
        }
        return Inertia::render('Admin/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Stage 1: credentials. Auth::validate() (not attempt) so the
        // session isn't promoted until is_active + 2FA also pass.
        if (!Auth::validate($credentials)) {
            Log::info('auth.login_failed', ['email' => $credentials['email'], 'ip' => $request->ip()]);
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
        }

        $user = User::where('email', $credentials['email'])->first();

        // Stage 2: is_active
        if ($user->is_active === false) {
            Log::warning('auth.login_blocked_inactive', ['user_id' => $user->id, 'ip' => $request->ip()]);
            return back()->withErrors(['email' => 'تم تعطيل هذا الحساب — تواصل مع مدير النظام.']);
        }

        // Stage 3: 2FA challenge if confirmed
        if ($user->two_factor_confirmed_at !== null) {
            $request->session()->put('auth.2fa.user_id', $user->id);
            $request->session()->put('auth.2fa.remember', $request->boolean('remember'));
            $request->session()->put('auth.2fa.expires_at', now()->addMinutes(15)->timestamp);
            return redirect()->route('admin.2fa.challenge');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $this->stampLogin($user, $request);

        return redirect()->intended('/admin');
    }

    public function showTwoFactorChallenge(Request $request)
    {
        if (!$request->session()->has('auth.2fa.user_id')) {
            return redirect()->route('admin.login');
        }
        return Inertia::render('Admin/TwoFactorChallenge');
    }

    public function verifyTwoFactor(Request $request, TwoFactorService $twoFactor)
    {
        $userId = $request->session()->get('auth.2fa.user_id');
        $expires = (int) $request->session()->get('auth.2fa.expires_at', 0);

        if (!$userId || $expires < time()) {
            $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.remember', 'auth.2fa.expires_at']);
            return redirect()->route('admin.login')
                ->withErrors(['code' => 'انتهت صلاحية الجلسة، أعد تسجيل الدخول.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('admin.login');
        }

        $code = (string) $request->input('code', '');
        $isRecovery = (bool) $request->input('is_recovery', false);

        $passed = $isRecovery
            ? $twoFactor->consumeRecoveryCode($user, $code)
            : $twoFactor->verify($twoFactor->decryptSecret($user) ?? '', $code);

        if (!$passed) {
            Log::warning('auth.2fa_failed', ['user_id' => $user->id, 'ip' => $request->ip()]);
            return back()->withErrors(['code' => 'الكود غير صحيح']);
        }

        $remember = (bool) $request->session()->pull('auth.2fa.remember');
        $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.expires_at']);
        Auth::login($user, $remember);
        $request->session()->regenerate();
        $this->stampLogin($user, $request);

        Log::info('auth.2fa_success', ['user_id' => $user->id, 'ip' => $request->ip(), 'recovery' => $isRecovery]);
        return redirect()->intended('/admin');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($userId) {
            Log::info('auth.logout', ['user_id' => $userId, 'ip' => $request->ip()]);
        }
        return redirect('/admin/login');
    }

    protected function stampLogin(User $user, Request $request): void
    {
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();
        Log::info('auth.login_success', ['user_id' => $user->id, 'ip' => $request->ip()]);
    }
}
