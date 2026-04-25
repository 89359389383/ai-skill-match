<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $isStripeAuthFlow =
            $request->is('skills/orders/*/checkout/success')
            || $request->is('skills/orders/*/checkout/cancel')
            || $request->is('login');

        $logContext = [
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'query_slot' => $request->query('slot'),
            'slot_attr' => $request->attributes->get('slot'),
            'resolved_slot_attr' => $request->attributes->get('resolved_slot'),
            'session_cookie_name' => config('session.cookie'),
            'has_session_cookie' => $request->cookies->has((string) config('session.cookie')),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'intended_in_session' => $request->hasSession() ? $request->session()->get('url.intended') : null,
        ];

        // 認証判定が発生する箇所を絞って詳細ログを出す
        if ($isStripeAuthFlow) {
            try {
                Log::info('[EnsureAuthenticatedAnyGuard] entering handle', $logContext + [
                    'guards' => $guards,
                    'expects_json' => $request->expectsJson(),
                    'referer' => $request->header('Referer'),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {
                // ログ失敗は本挙動へ影響させない
            }
        }

        // guards を省略された場合は、標準の web ガード相当でチェックする
        if (empty($guards)) {
            $guards = [null];
        }

        $guardChecks = [];
        // 指定されたガードを順番に見て、ログインしているガードが1つでもあればOK
        foreach ($guards as $guard) {
            $guardChecks[(string) $guard] = Auth::guard($guard)->check();

            if ($guardChecks[(string) $guard] === true) {
                // “このリクエストではこのガードを使う” と宣言しておく（後続の user 取得が楽になる）
                Auth::shouldUse($guard);

                if ($isStripeAuthFlow) {
                    try {
                        Log::info('[EnsureAuthenticatedAnyGuard] auth allowed (guard matched)', $logContext + [
                            'matched_guard' => $guard,
                            'guard_checks' => $guardChecks,
                            'auth_user_id' => optional(Auth::guard($guard)->user())->id,
                            'auth_user_role' => optional(Auth::guard($guard)->user())->role,
                        ]);
                    } catch (\Throwable $e) {
                    }
                }

                return $next($request);
            }
        }

        // ここまで来たら「どのガードでも未ログイン」
        // - ブラウザアクセスはログイン画面へ誘導（guest()で元のURLをセッションに保存し、ログイン後に戻れるようにする）
        // - APIアクセス（JSON期待）は 401 を返す
        if ($request->expectsJson()) {
            abort(401, 'Unauthenticated.');
        }

        if ($isStripeAuthFlow) {
            try {
                Log::warning('[EnsureAuthenticatedAnyGuard] auth denied -> redirect to login', $logContext + [
                    'guards' => $guards,
                    'guard_checks' => $guardChecks,
                    'redirect_full_url' => $request->fullUrl(),
                ]);
            } catch (\Throwable $e) {
            }
        }

        // Stripe の決済成功/失敗から戻ってきたタイミングでセッションが未判定になることがあるため、
        // ログイン後に「元の遷移先へ戻す」ための intended URL を保存する。
        // AuthController@showLoginForm が `redirect` クエリから session('url.intended') を設定する前提。
        return redirect()->guest(route('auth.login.form', [
            'redirect' => $request->fullUrl(),
        ]));
    }
}

