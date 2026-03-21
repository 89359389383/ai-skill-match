<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetSessionCookieSlot
{
    public function handle(Request $request, Closure $next)
    {
        $slot = $this->resolveSlot($request);
        if (!is_string($slot) || $slot === '') {
            return $next($request);
        }

        // クッキー名に使うため、危険文字を除去
        $safeSlot = preg_replace('/[^A-Za-z0-9_-]/', '', $slot) ?: 'default';

        // アプリ名ベース + slot でセッションクッキーを分離
        $base = Str::slug(env('APP_NAME', 'laravel'), '_');
        config(['session.cookie' => $base . '_session_' . $safeSlot]);

        // 後段（リダイレクトのクエリ付与など）で参照できるように属性へ保持
        $request->attributes->set('slot', $safeSlot);
        // Blade の hidden / route() 用（Referer から解決した slot も含む・元の文字列）
        $request->attributes->set('resolved_slot', $slot);

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

