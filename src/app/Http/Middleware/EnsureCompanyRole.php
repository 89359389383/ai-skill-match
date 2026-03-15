<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyRole
{
    /**
     * 企業以外のアクセスをブロックする。
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // このリクエストでは company guard を“標準”として扱う（Auth::user()等がズレないようにする）
        Auth::shouldUse('company');

        // 未ログインなら、ログイン画面へ誘導する（guest()で元のURLを保存し、ログイン後に戻れるようにする）
        if (!auth('company')->check()) {
            return redirect()->guest(route('auth.login.form'));
        }

        // roleがcompany以外なら、権限エラーとして拒否する
        if (auth('company')->user()->role !== 'company') {
            abort(403, '企業権限が必要です。');
        }

        return $next($request);
    }
}

