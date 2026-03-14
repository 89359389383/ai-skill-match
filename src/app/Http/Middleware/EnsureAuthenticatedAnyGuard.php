<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedAnyGuard
{
    /**
     * 複数ガードの「どれか」でログインしていれば通すミドルウェア。
     *
     * なぜ必要？
     * - このアプリは guard を freelancer / company で分けている
     * - 記事投稿・質問投稿は「どちらのユーザーでもOK」にしたい
     * - Laravel標準の `auth:guard1,guard2` は「OR条件」にならないため
     *
     * 使い方（routes/web.php）:
     * - Route::middleware(['auth.any:freelancer,company'])->group(...)
     *
     * 補足:
     * - ここで Auth::shouldUse($guard) を設定しておくと、
     *   以降の処理で request()->user() / Auth::user() が “当たったガードのユーザー” になる。
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        // guards を省略された場合は、標準の web ガード相当でチェックする
        if (empty($guards)) {
            $guards = [null];
        }

        // 指定されたガードを順番に見て、ログインしているガードが1つでもあればOK
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // “このリクエストではこのガードを使う” と宣言しておく（後続の user 取得が楽になる）
                Auth::shouldUse($guard);
                return $next($request);
            }
        }

        // ここまで来たら「どのガードでも未ログイン」
        // - ブラウザアクセスはログイン画面へ誘導
        // - APIアクセス（JSON期待）は 401 を返す
        if ($request->expectsJson()) {
            abort(401, 'Unauthenticated.');
        }

        return redirect()->route('auth.login.form');
    }
}

