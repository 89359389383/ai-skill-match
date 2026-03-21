<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * スキル出品まわりの 419 調査用（トークン値はログに出さない）
     */
    protected function tokensMatch($request)
    {
        $match = parent::tokensMatch($request);

        if (!$match && ($request->is('skills') || $request->is('skills/*'))) {
            Log::warning('[VerifyCsrfToken] CSRF 不一致（Page Expired の原因候補）', [
                'path' => $request->path(),
                'method' => $request->method(),
                'session_cookie_name' => config('session.cookie'),
                'query_slot' => $request->query('slot'),
                'input_slot' => $request->input('slot'),
                'has__token' => $request->has('_token'),
                '_token_length' => $request->has('_token') ? strlen((string) $request->input('_token')) : 0,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            ]);
        }

        return $match;
    }
}