<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetSessionCookieSlot
{
    public function handle(Request $request, Closure $next)
    {
        $slot = $request->query('slot') ?? $request->input('slot');
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

        return $next($request);
    }
}

