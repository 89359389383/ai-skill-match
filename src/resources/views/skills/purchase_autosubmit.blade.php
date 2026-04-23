<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>決済へ移動</title>
</head>
<body onload="document.getElementById('purchase-form').submit()">
<form id="purchase-form" method="POST" action="{{ route('skills.purchase', ['skill_listing' => $skill_listing->id]) }}">
    @csrf

    {{-- slot は SetSessionCookieSlot / PreserveSlotOnRedirect で使うため、GETに付いていたものを再投入 --}}
    @if (request()->filled('slot'))
        <input type="hidden" name="slot" value="{{ request('slot') }}">
    @endif
</form>

<p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans JP', 'Hiragino Sans', 'Hiragino Kaku Gothic ProN', 'Yu Gothic', 'Meiryo', sans-serif;">
    決済画面へ移動しています。少々お待ちください。
</p>
</body>
</html>

