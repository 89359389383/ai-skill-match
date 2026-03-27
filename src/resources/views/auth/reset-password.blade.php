<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再設定 - AITECH</title>
    <link rel="icon" href="{{ asset('aifavicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* auth/login と同じ背景グラデーション */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(ellipse at top left, rgba(255, 237, 213, 0.85) 0%, transparent 55%),
                radial-gradient(ellipse at bottom right, rgba(253, 186, 116, 0.55) 0%, transparent 55%),
                radial-gradient(ellipse at center, rgba(255, 246, 230, 0.55) 0%, transparent 70%),
                linear-gradient(135deg, #fff7ed 0%, #ffedd5 20%, #fed7aa 45%, #ffedd5 70%, #fff7ed 100%);
            overflow: hidden;
            z-index: 0;
        }
    </style>
</head>

<body>
    <div class="background"></div>

    <div class="min-h-screen flex items-center justify-center px-4 md:px-6 lg:px-8 py-10 relative z-10">
        <div class="w-full max-w-md md:max-w-lg bg-white/90 backdrop-blur-xl border border-white/70 rounded-xl shadow-xl p-6 md:p-10">
            <h1 class="text-center text-xl md:text-2xl font-black tracking-tight text-slate-900">パスワード再設定</h1>

            @include('partials.error-panel')

            <form class="mt-6 space-y-4" method="POST" action="{{ route('password.update') }}">
                @csrf
                @include('partials.session-slot-field')

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request()->email }}">

                <div>
                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-md border border-slate-200 bg-white px-4 py-3 text-sm md:text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('password') border-red-500 @enderror"
                        maxlength="128"
                        placeholder="新しいパスワード">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full rounded-md border border-slate-200 bg-white px-4 py-3 text-sm md:text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        maxlength="128"
                        placeholder="新しいパスワード（確認）">
                </div>

                <button type="submit" class="w-full rounded-md bg-[#FC4C0C] hover:bg-[#f14005] px-4 py-3 text-sm md:text-base font-extrabold text-white shadow hover:shadow-md active:shadow">
                    パスワードを設定
                </button>
            </form>
        </div>
    </div>

</body>

</html>