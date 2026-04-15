<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureBuyerRole
{
    /**
     * 購入者以外のアクセスをブロックする。
     */
    public function handle(Request $request, Closure $next)
    {
        // このリクエストでは buyer guard を“標準”として扱う
        Auth::shouldUse('buyer');

        if (!auth('buyer')->check()) {
            return redirect()->guest(route('auth.login.form', ['redirect' => $request->fullUrl()]));
        }

        if (auth('buyer')->user()->role !== 'buyer') {
            abort(403, '購入者権限が必要です。');
        }

        return $next($request);
    }
}

