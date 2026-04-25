<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureBuyerRole
{
    /**
     * 購入者以外のアクセスをブロックする。
     */
    public function handle(Request $request, Closure $next)
    {
        $isStripeAuthFlow =
            $request->is('skills/orders/*/checkout/success')
            || $request->is('skills/orders/*/checkout/cancel')
            || $request->is('login');

        if ($isStripeAuthFlow) {
            try {
                Log::info('[EnsureBuyerRole] entering handle', [
                    'path' => $request->path(),
                    'full_url' => $request->fullUrl(),
                    'query_slot' => $request->query('slot'),
                    'slot_attr' => $request->attributes->get('slot'),
                    'resolved_slot_attr' => $request->attributes->get('resolved_slot'),
                    'session_cookie_name' => config('session.cookie'),
                    'has_session_cookie' => $request->cookies->has((string) config('session.cookie')),
                    'buyer_guard_check' => Auth::guard('buyer')->check(),
                    'expects_json' => $request->expectsJson(),
                ]);
            } catch (\Throwable $e) {
            }
        }

        // このリクエストでは buyer guard を“標準”として扱う
        Auth::shouldUse('buyer');

        if (!auth('buyer')->check()) {
            if ($isStripeAuthFlow) {
                try {
                    Log::warning('[EnsureBuyerRole] buyer not authenticated -> redirect to login', [
                        'path' => $request->path(),
                        'full_url' => $request->fullUrl(),
                        'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                        'intended_in_session' => $request->hasSession() ? $request->session()->get('url.intended') : null,
                    ]);
                } catch (\Throwable $e) {
                }
            }
            return redirect()->guest(route('auth.login.form', ['redirect' => $request->fullUrl()]));
        }

        if (auth('buyer')->user()->role !== 'buyer') {
            if ($isStripeAuthFlow) {
                try {
                    Log::warning('[EnsureBuyerRole] authenticated but role mismatch', [
                        'path' => $request->path(),
                        'full_url' => $request->fullUrl(),
                        'buyer_user_id' => auth('buyer')->user()?->id,
                        'buyer_user_role' => auth('buyer')->user()?->role,
                    ]);
                } catch (\Throwable $e) {
                }
            }
            abort(403, '購入者権限が必要です。');
        }

        return $next($request);
    }
}

