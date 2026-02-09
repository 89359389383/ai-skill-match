<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - AITECH</title>
    <style>
        /* リセット & ベース */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Hiragino Sans', 'Hiragino Kaku Gothic ProN', 'Yu Gothic', sans-serif;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
            color: #0f172a;
        }

        /* 背景デザイン - 薄い青色ベース */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(ellipse at top left, rgba(219, 234, 254, 0.75) 0%, transparent 55%),
                radial-gradient(ellipse at bottom right, rgba(186, 230, 253, 0.55) 0%, transparent 55%),
                radial-gradient(ellipse at center, rgba(224, 242, 254, 0.45) 0%, transparent 70%),
                linear-gradient(135deg, #eff6ff 0%, #dbeafe 20%, #bae6fd 45%, #e0f2fe 70%, #f0f9ff 100%);
            overflow: hidden;
            z-index: 0;
        }

        /* コンテナ */
        .container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* カード */
        .login-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(24px) saturate(180%);
            border-radius: 12px;
            padding: 40px 64px;
            width: 100%;
            max-width: 560px;
            box-shadow:
                0 25px 70px rgba(30, 136, 229, 0.18),
                0 10px 40px rgba(3, 169, 244, 0.10),
                0 0 100px rgba(255, 255, 255, 0.35),
                inset 0 1px 1px rgba(255, 255, 255, 1),
                inset 0 -1px 1px rgba(30, 136, 229, 0.08);
            position: relative;
            animation: cardFadeIn 0.6s ease-out, glow 5s ease-in-out infinite;
            border: 1px solid rgba(255, 255, 255, 0.7);
        }

        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow:
                    0 25px 70px rgba(30, 136, 229, 0.18),
                    0 10px 40px rgba(3, 169, 244, 0.10),
                    0 0 100px rgba(255, 255, 255, 0.35),
                    inset 0 1px 1px rgba(255, 255, 255, 1),
                    inset 0 -1px 1px rgba(30, 136, 229, 0.08);
            }

            50% {
                box-shadow:
                    0 30px 80px rgba(30, 136, 229, 0.24),
                    0 15px 50px rgba(3, 169, 244, 0.16),
                    0 0 120px rgba(255, 255, 255, 0.45),
                    0 0 40px rgba(100, 181, 246, 0.16),
                    inset 0 1px 1px rgba(255, 255, 255, 1),
                    inset 0 -1px 1px rgba(30, 136, 229, 0.10);
            }
        }

        /* ロゴ */
        .logo {
            display: flex;
            align-items: baseline;
            justify-content: center;
            margin-bottom: 18px;
            gap: 10px;
        }

        .logo-text {
            font-weight: 900;
            font-size: 22px;
            letter-spacing: 0.5px;
            color: #0b1220;
        }

        .logo-badge {
            background: #0b1220;
            color: #fff;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 800;
            font-style: italic;
        }

        .page-title {
            text-align: center;
            font-weight: 900;
            font-size: 24px;
            color: #0f172a;
            letter-spacing: 2px;
            margin-bottom: 18px;
        }

        /* 成功/エラー */
        .success-box {
            background: rgba(16, 185, 129, 0.10);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #065f46;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .error-box {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.20);
            color: #7f1d1d;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        /* フォーム */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #fff;
            outline: none;
        }

        .form-input::placeholder {
            color: #bdbdbd;
        }

        .form-input:focus {
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .form-input.is-invalid {
            border-color: rgba(239, 68, 68, 0.8);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
        }

        .error-message {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 800;
            color: #dc2626;
        }

        /* パスワード表示切替 */
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9e9e9e;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: #666;
        }

        .toggle-password.active {
            color: #1e88e5;
        }

        .eye-icon {
            width: 20px;
            height: 20px;
        }

        /* ボタン */
        .login-button {
            width: 100%;
            padding: 12px 24px;
            background: linear-gradient(135deg, #1e88e5 0%, #1976d2 100%);
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(30, 136, 229, 0.25);
            margin-top: 8px;
        }

        .login-button:hover {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            box-shadow: 0 6px 16px rgba(30, 136, 229, 0.32);
            transform: translateY(-1px);
        }

        .login-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(30, 136, 229, 0.22);
        }

        .register-links {
            margin-top: 16px;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .register-links a {
            color: #475569;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .register-links a:hover {
            color: #1e88e5;
            text-decoration: underline;
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="background"></div>

    <div class="container px-4 md:px-6 lg:px-8 py-10">
        <div class="login-card p-6 md:p-10 lg:p-12">
            <div class="page-title">複業AI</div>
            @include('partials.error-panel')

            @if (session('success'))
            <div class="success-box">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="error-box">
                {{ session('error') }}
            </div>
            @endif

            <form class="login-form" method="POST">
                @csrf

                <div class="form-group">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="form-input @error('email') is-invalid @enderror"
                        placeholder="メールアドレス"
                        autocomplete="email">
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="password-wrapper">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-input @error('password') is-invalid @enderror"
                            placeholder="パスワード"
                            autocomplete="current-password">
                        <button
                            type="button"
                            class="toggle-password"
                            id="togglePassword"
                            onclick="togglePasswordVisibility('password', 'togglePassword')"
                            aria-label="パスワード表示切替">
                            <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="login-button"
                    formaction="{{ route('auth.login.freelancer') }}">
                    フリーランスでログイン
                </button>

                <button
                    type="submit"
                    class="login-button"
                    formaction="{{ route('auth.login.company') }}">
                    企業でログイン
                </button>

                <div class="register-links">
                    <a href="{{ route('password.request') }}">パスワードを忘れた方はこちら</a>
                    <a href="{{ route('auth.register.freelancer.form') }}">フリーランスとして新規登録</a>
                    <a href="{{ route('auth.register.company.form') }}">企業として新規登録</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, buttonId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = document.getElementById(buttonId);
            if (!passwordInput || !toggleButton) return;
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.add('active');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('active');
            }
        }
    </script>
</body>

</html>

