<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PreserveSlotOnRedirect
{
    public function handle(Request $request, Closure $next)
    {
        $slot = $request->query('slot') ?? $request->input('slot');
        $response = $next($request);

        if (!$slot || !($response instanceof RedirectResponse)) {
            return $response;
        }

        $targetUrl = $response->getTargetUrl();
        if (!is_string($targetUrl) || $targetUrl === '') {
            return $response;
        }

        // すでに slot が付いていれば追加しない
        if (str_contains($targetUrl, 'slot=') || str_contains($targetUrl, '&slot=')) {
            return $response;
        }

        $delimiter = str_contains($targetUrl, '?') ? '&' : '?';
        $newTargetUrl = $targetUrl . $delimiter . 'slot=' . urlencode((string) $slot);

        $response->setTargetUrl($newTargetUrl);
        return $response;
    }
}

