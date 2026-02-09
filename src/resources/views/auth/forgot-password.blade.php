<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード再設定 - AITECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="fixed inset-0 -z-10 bg-gradient-to-br from-sky-50 via-blue-50 to-cyan-100"></div>

    <div class="min-h-screen flex items-center justify-center px-4 md:px-6 lg:px-8 py-10">
        <div class="w-full max-w-md md:max-w-lg bg-white/90 backdrop-blur-xl border border-white/70 rounded-xl shadow-xl p-6 md:p-10">
            <h1 class="text-center text-xl md:text-2xl font-black tracking-tight text-slate-900">パスワード再設定</h1>
            <p class="mt-3 text-center text-sm md:text-base text-slate-600 leading-relaxed">
                登録されているメールアドレスを入力してください。<br class="hidden md:block">
                パスワード再設定用のメールを送信します。
            </p>

            @if (session('status'))
                <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-bold flex items-start gap-2">
                    <svg class="mt-0.5 h-5 w-5 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-bold">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <input
                        type="email"
                        name="email"
                        class="w-full rounded-md border border-slate-200 bg-white px-4 py-3 text-sm md:text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        placeholder="メールアドレス"
                        required>
                </div>

                <button type="submit" class="w-full rounded-md bg-gradient-to-br from-blue-600 to-blue-700 px-4 py-3 text-sm md:text-base font-extrabold text-white shadow hover:shadow-md active:shadow">
                    送信
                </button>

                <div class="pt-1 text-center">
                    <a class="text-sm md:text-base font-extrabold text-slate-600 hover:text-blue-600 hover:underline" href="{{ route('auth.login.form') }}">
                        ログインページに戻る
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>

</html>