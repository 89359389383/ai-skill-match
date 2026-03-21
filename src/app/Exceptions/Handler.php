<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // 419: multipart 全体を all() でログに載せない（VerifyCsrfToken 側でも詳細ログあり）
        if ($exception instanceof TokenMismatchException) {
            Log::warning('【419 Page Expired】CSRF / セッション不一致', [
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'method' => $request->method(),
                'session_cookie_name' => config('session.cookie'),
                'query_slot' => $request->query('slot'),
                'input_slot' => $request->input('slot'),
                'has__token' => $request->has('_token'),
                '_token_length' => $request->has('_token') ? strlen((string) $request->input('_token')) : 0,
            ]);

            return parent::render($request, $exception);
        }

        // ログインユーザー情報
        $user = Auth::check() ? [
            'id' => Auth::user()->id,
            'email' => Auth::user()->email,
            'role' => Auth::user()->role,
        ] : 'Guest';

        // **エラー時のログ（重要な情報のみ記録）**
        if ($exception) {
            Log::error('【例外キャッチ】', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'url' => $request->url(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'params' => $request->all(),
                'user' => $user,
                'status_code' => $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException ? $exception->getStatusCode() : 500,
                'env_config' => [
                    'APP_ENV' => env('APP_ENV'),
                    'SESSION_DRIVER' => env('SESSION_DRIVER'),
                    'CACHE_DRIVER' => env('CACHE_DRIVER'),
                ],
                'trace' => $exception->getTraceAsString(),
            ]);

            // 例外オブジェクト全体をそのままログに出力（補足ログ）
            Log::error($exception);
        }

        return parent::render($request, $exception);
    }
}
