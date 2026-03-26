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

    {{-- 画面ごとの追加CSS（top/index など） --}}
    @stack('styles')
</head>
<body class="bg-gray-50">
    {{-- ログイン不要画面用：共通ヘッダー（固定） --}}
    @include('partials.public-header')

    {{-- 固定ヘッダー分の余白（共通ヘッダー1段分） --}}
    <main class="pt-16">
        {{-- 共通のエラーパネル表示は無効化（ページ個別で制御） --}}
        {{-- @include('partials.error-panel') --}}
        @yield('content')
    </main>

    {{-- ログイン不要画面用：共通フッター --}}
    @include('partials.public-footer')

    {{-- 画面ごとの追加JS（top/index など） --}}
    @stack('scripts')
</body>
</html>

