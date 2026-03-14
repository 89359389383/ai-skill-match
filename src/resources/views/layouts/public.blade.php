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
    </style>

    {{-- 画面ごとの追加CSS（top/index など） --}}
    @stack('styles')
</head>
<body class="bg-gray-50">
    {{-- ログイン不要画面用：共通ヘッダー --}}
    @include('partials.public-header')

    <main>
        @yield('content')
    </main>

    {{-- ログイン不要画面用：共通フッター --}}
    @include('partials.public-footer')

    {{-- 画面ごとの追加JS（top/index など） --}}
    @stack('scripts')
</body>
</html>

