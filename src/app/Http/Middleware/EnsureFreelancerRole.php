<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureFreelancerRole
{
    /**
     * フリーランス以外のアクセスをブロックする。
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // このリクエストでは freelancer guard を“標準”として扱う（Auth::user()等がズレないようにする）
        Auth::shouldUse('freelancer');

        // 未ログインなら、ログイン画面へ誘導する（guest()で元のURLを保存し、ログイン後に戻れるようにする）
        if (!auth('freelancer')->check()) {
            return redirect()->guest(route('auth.login.form'));
        }

        // roleがfreelancer以外なら、権限エラーとして拒否する
        if (auth('freelancer')->user()->role !== 'freelancer') {
            abort(403, 'フリーランス権限が必要です。');
        }

        return $next($request);
    }
}

