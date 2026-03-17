<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AIスキルマッチ')</title>

    {{-- CDN版Tailwind（まずは素早く画面を出すため） --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        :root {
            --public-header-height: 64px;
            --freelancer-header-height: 72px;
            --main-pt-freelancer: calc(var(--public-header-height) + var(--freelancer-header-height) + 40px);
        }
    </style>

    {{-- フリーランスログイン時：専用ヘッダーのスタイル --}}
    @auth('freelancer')
        @include('partials.freelancer-header-style')
    @endauth

    {{-- 企業ログイン時：専用ヘッダーのスタイル --}}
    @auth('company')
        @include('partials.company-header-style')
    @endauth

    {{-- 画面ごとの追加CSS（top/index など） --}}
    @stack('styles')
</head>
<body class="bg-gray-50">
    {{-- ログイン不要画面用：共通ヘッダー（固定） --}}
    @include('partials.public-header')

    {{-- フリーランスログイン時：専用ヘッダー（スキル販売・記事・取引などでも表示維持） --}}
    @auth('freelancer')
        @include('partials.freelancer-header')
    @endauth

    {{-- 企業ログイン時：専用ヘッダー（スキル購入・取引などでも表示維持） --}}
    @auth('company')
        @include('partials.company-header')
    @endauth

    {{-- 固定ヘッダー分の余白（ログイン時は2段ヘッダー分 + 8px余裕、未ログイン時は1段のみ） --}}
    <main class="@if(auth('freelancer')->check() || auth('company')->check()) pt-[var(--main-pt-freelancer)] @else pt-16 @endif">
        @yield('content')
    </main>

    {{-- ログイン不要画面用：共通フッター --}}
    @include('partials.public-footer')

    {{-- 画面ごとの追加JS（top/index など） --}}
    @stack('scripts')
</body>
</html>

