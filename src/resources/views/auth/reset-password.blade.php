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

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-bold">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form class="mt-6 space-y-4" method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request()->email }}">

                <div>
                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-md border border-slate-200 bg-white px-4 py-3 text-sm md:text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        placeholder="新しいパスワード"
                        required>
                </div>

                <div>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full rounded-md border border-slate-200 bg-white px-4 py-3 text-sm md:text-base outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        placeholder="新しいパスワード（確認）"
                        required>
                </div>

                <button type="submit" class="w-full rounded-md bg-gradient-to-br from-blue-600 to-blue-700 px-4 py-3 text-sm md:text-base font-extrabold text-white shadow hover:shadow-md active:shadow">
                    パスワードを設定
                </button>
            </form>
        </div>
    </div>

</body>

</html>