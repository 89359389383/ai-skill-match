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
        'stripe/webhook',
    ];

    /**
     * スキル出品まわりの 419 調査用（トークン値はログに出さない）
     */
    protected function tokensMatch($request)
    {
        $match = parent::tokensMatch($request);

        $detailLogPaths = $request->is('skills')
            || $request->is('skills/*')
            || $request->is('register/company')
            || $request->is('company/profile')
            || $request->is('company/profile/*');

        if (!$match && $detailLogPaths) {
            Log::warning('[VerifyCsrfToken] CSRF 不一致（Page Expired の原因候補）', [
                'path' => $request->path(),
                'method' => $request->method(),
                'session_cookie_name' => config('session.cookie'),
                'query_slot' => $request->query('slot'),
                'input_slot' => $request->input('slot'),
                'resolved_slot_attr' => $request->attributes->get('slot'),
                'referer' => $request->header('Referer'),
                'has__token' => $request->has('_token'),
                '_token_length' => $request->has('_token') ? strlen((string) $request->input('_token')) : 0,
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'csrf_token_length_session' => $request->hasSession() ? strlen((string) $request->session()->token()) : null,
            ]);
        }

        return $match;
    }
}