<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>企業プロフィール登録 - AITECH</title>
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
            align-items: flex-start;
            justify-content: center;
            padding: 40px 20px;
        }

        /* カード */
        .register-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(24px) saturate(180%);
            border-radius: 12px;
            padding: 40px 64px;
            width: 100%;
            max-width: 720px;
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
            font-size: 18px;
            color: #0f172a;
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
        .register-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            position: relative;
        }

        .label {
            display: block;
            font-size: 13px;
            font-weight: 900;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-input,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #fff;
            outline: none;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: #bdbdbd;
        }

        .form-input:focus,
        .form-textarea:focus {
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .form-input.is-invalid,
        .form-textarea.is-invalid {
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

        .help {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
        }

        /* ボタン */
        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e88e5 0%, #1976d2 100%);
            color: #fff;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(30, 136, 229, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            box-shadow: 0 6px 16px rgba(30, 136, 229, 0.32);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(15, 23, 42, 0.08);
            color: #0f172a;
            font-size: 16px;
        }

        .btn-secondary:hover {
            background: rgba(15, 23, 42, 0.12);
            transform: translateY(-1px);
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="background"></div>

    <div class="container px-4 md:px-6 lg:px-8 py-8 md:py-12">
        <div class="register-card w-full max-w-2xl bg-white/90 backdrop-blur-xl border border-white/70 rounded-xl p-6 md:p-10 lg:p-12 shadow-[0_25px_70px_rgba(30,136,229,0.18)]">
            <div class="page-title text-lg md:text-xl font-black text-center">企業プロフィール登録</div>
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

            <form class="register-form" method="POST" action="{{ route('company.profile.store') }}">
                @csrf

                <div class="form-group">
                    <label class="label" for="company_name">企業名（必須）</label>
                    <input
                        id="company_name"
                        type="text"
                        name="company_name"
                        value="{{ old('company_name') }}"
                        class="form-input @error('company_name') is-invalid @enderror"
                        placeholder="例: 株式会社AITECH"
>
                    @error('company_name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="label" for="overview">会社概要（任意）</label>
                    <textarea
                        id="overview"
                        name="overview"
                        class="form-textarea @error('overview') is-invalid @enderror"
                        placeholder="事業内容や特徴など（2000文字以内）">{{ old('overview') }}</textarea>
                    @error('overview')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="two-col grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="label" for="contact_name">担当者名（任意）</label>
                        <input
                            id="contact_name"
                            type="text"
                            name="contact_name"
                            value="{{ old('contact_name') }}"
                            class="form-input @error('contact_name') is-invalid @enderror"
                            placeholder="例: 山田 太郎">
                        @error('contact_name')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="label" for="department">部署（任意）</label>
                        <input
                            id="department"
                            type="text"
                            name="department"
                            value="{{ old('department') }}"
                            class="form-input @error('department') is-invalid @enderror"
                            placeholder="例: 開発部">
                        @error('department')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="label" for="introduction">自己紹介（任意）</label>
                    <textarea
                        id="introduction"
                        name="introduction"
                        class="form-textarea @error('introduction') is-invalid @enderror"
                        placeholder="フリーランスに伝えたいこと（募集背景/働き方など・2000文字以内）">{{ old('introduction') }}</textarea>
                    @error('introduction')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                    <div class="help">登録後は「プロフィール設定」から更新できます</div>
                </div>

                <div class="btn-row flex flex-col md:flex-row gap-3">
                    <a class="btn btn-secondary w-full md:flex-1" href="{{ url()->previous() }}">戻る</a>
                    <button class="btn btn-primary w-full md:flex-1" type="submit">登録</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
