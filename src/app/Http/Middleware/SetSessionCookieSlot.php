<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SetSessionCookieSlot
{
    public function handle(Request $request, Closure $next)
    {
        $isStripeAuthFlow =
            $request->is('skills/orders/*/checkout/success')
            || $request->is('skills/orders/*/checkout/cancel')
            || $request->is('login');

        $slot = $this->resolveSlot($request);
        if (!is_string($slot) || $slot === '') {
            if ($isStripeAuthFlow) {
                try {
                    Log::warning('[SetSessionCookieSlot] slot not resolved', [
                        'path' => $request->path(),
                        'full_url' => $request->fullUrl(),
                        'incoming_slot_query' => $request->query('slot'),
                        'incoming_slot_input' => $request->input('slot'),
                        'resolved_slot_raw' => $slot,
                        'referer' => $request->header('Referer'),
                        'session_cookie_name_before' => config('session.cookie'),
                        'has_session_cookie_before' => $request->cookies->has((string) config('session.cookie')),
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }
            return $next($request);
        }

        // クッキー名に使うため、危険文字を除去
        $safeSlot = preg_replace('/[^A-Za-z0-9_-]/', '', $slot) ?: 'default';

        // アプリ名ベース + slot でセッションクッキーを分離
        $base = Str::slug(env('APP_NAME', 'laravel'), '_');
        $sessionCookieName = $base . '_session_' . $safeSlot;
        config(['session.cookie' => $sessionCookieName]);

        // 後段（リダイレクトのクエリ付与など）で参照できるように属性へ保持
        $request->attributes->set('slot', $safeSlot);
        // Blade の hidden / route() 用（Referer から解決した slot も含む・元の文字列）
        $request->attributes->set('resolved_slot', $slot);

        if ($isStripeAuthFlow) {
            try {
                Log::info('[SetSessionCookieSlot] slot resolved & cookie selected', [
                    'path' => $request->path(),
                    'full_url' => $request->fullUrl(),
                    'incoming_slot_query' => $request->query('slot'),
                    'incoming_slot_input' => $request->input('slot'),
                    'safe_slot' => $safeSlot,
                    'resolved_slot_raw' => $slot,
                    'session_cookie_name' => $sessionCookieName,
                    'has_session_cookie' => $request->cookies->has($sessionCookieName),
                ]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return $next($request);
    }

    /**
     * slot の解決順: クエリ → POST body → 同一ホストの Referer のクエリ
     *
     * 一覧が ?slot= 付きで、出品リンクだけ slot なしのとき GET /skills/new でも
     * Referer から slot を拾い、表示と POST で同じセッションCookieを使えるようにする。
     */
    private function resolveSlot(Request $request): ?string
    {
        $slot = $request->query('slot') ?? $request->input('slot');
        if (is_string($slot) && $slot !== '') {
            return $slot;
        }

        if (!$this->refererIsSameHost($request)) {
            return null;
        }

        $referer = (string) $request->headers->get('Referer');
        $queryString = parse_url($referer, PHP_URL_QUERY);
        if (!is_string($queryString) || $queryString === '') {
            return null;
        }

        parse_str($queryString, $parts);
        if (!empty($parts['slot']) && is_string($parts['slot']) && $parts['slot'] !== '') {
            return $parts['slot'];
        }

        return null;
    }

    private function refererIsSameHost(Request $request): bool
    {
        $referer = $request->headers->get('Referer');
        if (!is_string($referer) || $referer === '') {
            return false;
        }

        $refHost = parse_url($referer, PHP_URL_HOST);
        if (!is_string($refHost) || $refHost === '') {
            return false;
        }

        return strcasecmp($refHost, $request->getHost()) === 0;
    }
}

