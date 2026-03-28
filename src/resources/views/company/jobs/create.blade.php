<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>案件 新規登録（企業）- AITECH</title>
    <link rel="icon" href="{{ asset('aifavicon.png') }}">
    <style>
        :root {
            --header-height: 72px;           /* md 基本高さ */
            --header-height-mobile: 72px;     /* xs / mobile */
            --header-height-sm: 72px;         /* sm */
            --header-height-md: 72px;        /* md */
            --header-height-lg: 72px;        /* lg */
            --header-height-xl: 72px;        /* xl */
            --header-height-current: var(--header-height-mobile);
            --header-padding-x: 1rem;
        }

        /* Breakpoint: sm (>=640px) */
        @media (min-width: 640px) {
            :root {
                --header-padding-x: 1.5rem;
                --header-height-current: var(--header-height-sm);
            }
        }

        /* Breakpoint: md (>=768px) -- デスクトップの基本 */
        @media (min-width: 768px) {
            :root {
                --header-padding-x: 2rem;
                --header-height-current: var(--header-height-md);
            }
        }

        /* Breakpoint: lg (>=1024px) */
        @media (min-width: 1024px) {
            :root {
                --header-padding-x: 2.5rem;
                --header-height-current: var(--header-height-lg);
            }
        }

        /* Breakpoint: xl (>=1280px) */
        @media (min-width: 1280px) {
            :root {
                --header-padding-x: 3rem;
                --header-height-current: var(--header-height-xl);
            }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 97.5%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        /* Header (4 breakpoints: sm/md/lg/xl) */
        .header {
            background-color: #ffffff;
            border-bottom: 1px solid #e1e4e8;
            padding: 0 var(--header-padding-x);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            min-height: var(--header-height-current);
        }
        .header-content {
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr auto; /* mobile: ロゴ / 右側 */
            align-items: center;
            gap: 0.5rem;
            height: var(--header-height-current);
            position: relative;
            min-width: 0;
            padding: 0.25rem 0; /* 縦余白を確保 */
        }

        /* md以上: ロゴ / 中央ナビ / 右側 (ユーザー) */
        @media (min-width: 768px) {
            .header-content { grid-template-columns: auto 1fr auto; gap: 1rem; }
        }

        /* lg: より広く間隔を取る */
        @media (min-width: 1024px) {
            .header-content { gap: 1.5rem; padding: 0.5rem 0; }
        }

        .header-left { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }
        .header-right { display: flex; align-items: center; justify-content: flex-end; min-width: 0; gap: 0.75rem; }

        /* ロゴ（左） */
        .logo { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .logo-text {
            font-weight: 900;
            font-size: 18px;
            margin-left: 0;
            color: #111827;
            letter-spacing: 1px;
            white-space: nowrap;
        }
        @media (min-width: 640px) { .logo-text { font-size: 20px; } }
        @media (min-width: 768px) { .logo-text { font-size: 22px; } }
        @media (min-width: 1024px) { .logo-text { font-size: 24px; } }
        @media (min-width: 1280px) { .logo-text { font-size: 26px; } }
        .logo-badge {
            background: #0366d6;
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Mobile nav toggle */
        .nav-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid #e1e4e8;
            background: #fff;
            cursor: pointer;
            transition: all 0.15s ease;
            flex: 0 0 auto;
        }
        .nav-toggle:hover { background: #f6f8fa; }
        .nav-toggle:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.15); }
        .nav-toggle svg { width: 22px; height: 22px; color: #24292e; }
        @media (min-width: 768px) { .nav-toggle { display: none; } }

        .nav-links {
            display: none; /* mobile: hidden (use hamburger) */
            align-items: center;
            justify-content: center;
            flex-wrap: nowrap;
            min-width: 0;
            overflow: hidden;
            gap: 1.25rem;
        }
        @media (min-width: 640px) { .nav-links { display: none; } } /* smではまだ省スペースにすることが多い */
        @media (min-width: 768px) { .nav-links { display: flex; gap: 1.25rem; } }
        @media (min-width: 1024px) { .nav-links { gap: 2rem; } }
        @media (min-width: 1280px) { .nav-links { gap: 3rem; } }

        .nav-link {
            text-decoration: none;
            color: #586069;
            font-weight: 500;
            font-size: 1.05rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            transition: all 0.15s ease;
            position: relative;
            letter-spacing: -0.01em;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        @media (min-width: 768px) { .nav-link { font-size: 1.1rem; padding: 0.75rem 1.25rem; } }
        @media (min-width: 1280px) { .nav-link { font-size: 1.15rem; } }
        .nav-link.has-badge { padding-right: 3rem; }
        .nav-link:hover { background-color: #f6f8fa; color: #24292e; }
        .nav-link.active {
            background-color: #0366d6;
            color: white;
            box-shadow: 0 2px 8px rgba(3, 102, 214, 0.3);
        }
        .badge {
            background-color: #d73a49;
            color: white;
            border-radius: 50%;
            padding: 0.15rem 0.45rem;
            font-size: 0.7rem;
            font-weight: 600;
            min-width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(209, 58, 73, 0.3);
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
        .user-menu { display: flex; align-items: center; position: static; transform: none; }

        /* Mobile nav menu */
        .mobile-nav {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-bottom: 1px solid #e1e4e8;
            box-shadow: 0 16px 40px rgba(0,0,0,0.10);
            padding: 0.75rem var(--header-padding-x);
            display: none;
            z-index: 110;
        }
        .header.is-mobile-nav-open .mobile-nav { display: block; }
        @media (min-width: 768px) { .mobile-nav { display: none !important; } }
        .mobile-nav-inner {
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            gap: 0.5rem;
        }
        .mobile-nav .nav-link {
            width: 100%;
            justify-content: flex-start;
            background: #fafbfc;
            border: 1px solid #e1e4e8;
            padding: 0.875rem 1rem;
        }
        .mobile-nav .nav-link:hover { background: #f6f8fa; }
        .mobile-nav .nav-link.active {
            background-color: #0366d6;
            color: #fff;
            border-color: #0366d6;
        }
        .mobile-nav .nav-link.has-badge { padding-right: 1rem; }
        .mobile-nav .badge {
            position: static;
            transform: none;
            margin-left: auto;
            margin-right: 0;
        }

        /* Dropdown */
        .dropdown { position: relative; }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 240px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 12px;
            z-index: 1000;
            border: 1px solid #e1e4e8;
            margin-top: 0.5rem;
        }
        .dropdown.is-open .dropdown-content { display: block; }
        .dropdown-item {
            display: block;
            padding: 0.875rem 1.25rem;
            text-decoration: none;
            color: #586069;
            transition: all 0.15s ease;
            border-radius: 6px;
            margin: 0.25rem;
            white-space: nowrap;
        }
        .dropdown-item:hover { background-color: #f6f8fa; color: #24292e; }
        .dropdown-divider { height: 1px; background-color: #e1e4e8; margin: 0.5rem 0; }

        /* Layout (ページ固有のスタイルを以下に継承) */
        .main-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem;
            /* public-header は fixed なので、ヘッダー分だけ本文を下げる */
            padding-top: calc(4rem + 1.5rem);
        }
        @media (min-width: 768px) {
            .main-content { padding-top: calc(4rem + 2.5rem); }
        }
        .page-title { font-size: 2rem; font-weight: 900; margin-bottom: 1.5rem; letter-spacing: -0.025em; }
        .panel {
            background-color: white; border-radius: 16px; padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
        }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; }
        .field { margin-bottom: 1.25rem; }
        .field label { display: block; font-weight: 900; margin-bottom: 0.75rem; color: #586069; font-size: 0.9rem; }
        .input, .textarea, .select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .textarea { min-height: 140px; resize: vertical; }
        .input:focus, .textarea:focus, .select:focus { outline: none; border-color: #0366d6; box-shadow: 0 0 0 3px rgba(3,102,214,0.1); background-color: #fff; }
        .input.is-invalid, .textarea.is-invalid, .select.is-invalid {
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
        .help { margin-top: 0.5rem; color: #6a737d; font-weight: 800; font-size: 0.85rem; }
        .btn-row { display: flex; gap: 1rem; margin-top: 2rem; }
        .btn-row .btn { flex: 1; padding: 1rem; font-size: 16px; }
        .btn {
            padding: 0.875rem 1.75rem; border-radius: 10px; font-weight: 900;
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.15s ease; cursor: pointer; border: none; font-size: 0.95rem; white-space: nowrap;
        }
        .btn-primary {
            background: linear-gradient(90deg, #f97316 0%, #ef4444 100%);
            color: #fff;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #f97316 0%, #ef4444 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(249,115,22,0.3);
        }
        .btn-secondary { background-color: #586069; color: #fff; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }

        .error-panel {
            margin: 0 auto 1.5rem auto;
            background-color: rgb(255, 255, 255);
            border-color: rgb(255, 0, 0);
            padding: 14px 30px;
            max-width: 600px;
        }

        .error-panel .error-title {
            color: rgb(255, 0, 0);
            font-weight: 700;
        }

        .error-panel ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .error-panel li {
            margin-bottom: 8px;
        }

        .error-panel li:last-child {
            margin-bottom: 0;
        }

        .ymd-widget { width: 100%; }
        .ymd-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }
        .ymd-select {
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, #6b7280 50%),
                linear-gradient(135deg, #6b7280 50%, transparent 50%);
            background-position:
                calc(100% - 18px) calc(1.15em + 2px),
                calc(100% - 13px) calc(1.15em + 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            padding-right: 2.25rem;
        }
        .date-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            clip-path: inset(50%);
        }

        /* Required skills (multi inputs) */
        .skills-container { display: grid; gap: 0.75rem; }
        .skills-row-4 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }
        @media (min-width: 768px) {
            .skills-row-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        .skills-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.75rem;
            flex-wrap: wrap;
        }
        .skills-actions .btn {
            padding: 0.75rem 1rem;
            font-size: 14px;
            border-radius: 10px;
            border: 1px solid #e1e4e8;
            background: #fff;
            color: #111827;
        }
        .skills-actions .btn:hover { opacity: 0.92; }
        .skills-actions .btn:disabled { opacity: 0.55; cursor: not-allowed; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.public-header')

    <main class="main-content max-w-5xl mx-auto px-4 md:px-6 lg:px-8 pb-6 md:pb-10">
        <h1 class="page-title text-2xl md:text-3xl font-black tracking-tight">案件 新規登録</h1>
        
        @include('partials.error-panel')

        @if (session('success'))
            <div class="panel mb-6" style="background-color: #d4edda; border-color: #28a745;">
                <div style="color: #155724; font-weight: 700;">{{ session('success') }}</div>
            </div>
        @endif

        <div class="panel p-5 md:p-8">
            <form action="{{ route('company.jobs.store') }}" method="post">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                    <div class="field">
                        <label for="title">タイトル（必須）</label>
                        <input id="title" name="title" class="input @error('title') is-invalid @enderror" type="text" placeholder="例: ECサイト機能拡張プロジェクト" value="{{ old('title') }}">
                        @error('title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="subtitle">サブタイトル（必須）</label>
                        <input id="subtitle" name="subtitle" class="input @error('subtitle') is-invalid @enderror" type="text" placeholder="例: 週2〜/フルリモート/長期" value="{{ old('subtitle') }}">
                        @error('subtitle')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="companyName">会社名</label>
                        <input id="companyName" class="input" type="text" value="{{ Auth::user()->company->name ?? '未登録' }}" disabled>
                        <div class="help">企業プロフィールから自動取得されます</div>
                        @if (!Auth::user()->company)
                            <div class="help" style="color: #d73a49;">先に企業プロフィールを登録してください</div>
                        @endif
                    </div>
                </div>

                <div class="field">
                    <label for="description">案件概要（必須）</label>
                    <textarea id="description" name="description" class="textarea @error('description') is-invalid @enderror" placeholder="案件の概要を入力してください">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="desired_persona">求めている人物像（必須）</label>
                    <textarea id="desired_persona" name="desired_persona" class="textarea @error('desired_persona') is-invalid @enderror" placeholder="例: 主体的に課題を整理し、チームとコミュニケーションしながら改善できる方">{{ old('desired_persona') }}</textarea>
                    @error('desired_persona')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="required_skills_text">必要スキル（自由入力）</label>
                    @php
                        $requiredSkillsInvalid = $errors->has('required_skills_text');
                    @endphp
                    {{-- 互換性のため hidden にカンマ結合して送信 --}}
                    <input
                        id="required_skills_text"
                        name="required_skills_text"
                        type="hidden"
                        value="{{ old('required_skills_text') }}"
                    >
                    <div class="skills-container" id="required-skills-container" aria-label="必要スキル入力欄">
                        <div class="skills-row-4">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" placeholder="例：n8n" maxlength="500">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" maxlength="500">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" maxlength="500">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" maxlength="500">
                        </div>
                        <div class="skills-row-4">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" maxlength="500">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" maxlength="500">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" maxlength="500">
                            <input class="input skill-input {{ $requiredSkillsInvalid ? 'is-invalid' : '' }}" type="text" maxlength="500">
                        </div>
                    @error('required_skills_text')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                        @error('required_skills_text.*')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="skills-actions">
                        <button type="button" class="btn" id="required-skills-add-row">フォーム追加</button>
                        <button type="button" class="btn" id="required-skills-remove-row">削除</button>
                    </div>
                    <div class="help">各フォームに1つずつ入力できます。</div>
                </div>

                <div class="grid-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6">
                    <div class="field">
                        <label for="reward_type">報酬タイプ（必須）</label>
                        <select id="reward_type" name="reward_type" class="select @error('reward_type') is-invalid @enderror">
                            <option value="monthly" {{ old('reward_type', 'monthly') === 'monthly' ? 'selected' : '' }}>月額/案件単価</option>
                            <option value="hourly" {{ old('reward_type') === 'hourly' ? 'selected' : '' }}>時給</option>
                        </select>
                        @error('reward_type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="min_rate">最低単価（必須）</label>
                        <input
                            id="min_rate"
                            name="min_rate"
                            class="input @error('min_rate') is-invalid @enderror"
                            type="number"
                            placeholder="例: 300000"
                            value="{{ old('min_rate') }}"
                            inputmode="numeric"
                            step="1"
                            min="0"
                            max="100000000"
                            oninput="if(this.value===''){return;} this.value = Math.max(0, Math.min(100000000, Math.floor(Number(this.value))));"
                        >
                        <div class="help">円単位で入力してください</div>
                        @error('min_rate')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="max_rate">最高単価（必須）</label>
                        <input
                            id="max_rate"
                            name="max_rate"
                            class="input @error('max_rate') is-invalid @enderror"
                            type="number"
                            placeholder="例: 500000"
                            value="{{ old('max_rate') }}"
                            inputmode="numeric"
                            step="1"
                            min="0"
                            max="100000000"
                            oninput="if(this.value===''){return;} this.value = Math.max(0, Math.min(100000000, Math.floor(Number(this.value))));"
                        >
                        <div class="help">円単位で入力してください</div>
                        @error('max_rate')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="work_time_text">稼働条件（必須）</label>
                    <input id="work_time_text" name="work_time_text" class="input @error('work_time_text') is-invalid @enderror" type="text" placeholder="例: 週10〜20時間、2〜3ヶ月" value="{{ old('work_time_text') }}">
                    <div class="help">稼働時間や期間などを自由に入力してください</div>
                    @error('work_time_text')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                    <div class="field">
                        <label for="work_start_date">稼働開始（必須）</label>
                        <div class="ymd-widget" id="work_start_date_widget" data-hidden-input-id="work_start_date">
                            <div class="ymd-row">
                                <select class="input select ymd-select @error('work_start_date') is-invalid @enderror" data-role="year" aria-label="稼働開始 年">
                                    <option value="">年</option>
                                </select>
                                <select class="input select ymd-select @error('work_start_date') is-invalid @enderror" data-role="month" aria-label="稼働開始 月">
                                    <option value="">月</option>
                                </select>
                                <select class="input select ymd-select @error('work_start_date') is-invalid @enderror" data-role="day" aria-label="稼働開始 日">
                                    <option value="">日</option>
                                </select>
                            </div>
                        </div>
                        <input id="work_start_date" name="work_start_date" class="date-hidden" type="date" value="{{ old('work_start_date', date('Y-m-d')) }}">
                        @error('work_start_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="publish_end_date">掲載終了（必須）</label>
                        <div class="ymd-widget" id="publish_end_date_widget" data-hidden-input-id="publish_end_date">
                            <div class="ymd-row">
                                <select class="input select ymd-select @error('publish_end_date') is-invalid @enderror" data-role="year" aria-label="掲載終了 年">
                                    <option value="">年</option>
                                </select>
                                <select class="input select ymd-select @error('publish_end_date') is-invalid @enderror" data-role="month" aria-label="掲載終了 月">
                                    <option value="">月</option>
                                </select>
                                <select class="input select ymd-select @error('publish_end_date') is-invalid @enderror" data-role="day" aria-label="掲載終了 日">
                                    <option value="">日</option>
                                </select>
                            </div>
                        </div>
                        <input id="publish_end_date" name="publish_end_date" class="date-hidden" type="date" value="{{ old('publish_end_date', date('Y-m-d')) }}">
                        @error('publish_end_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="status">ステータス（必須）</label>
                    <select id="status" name="status" class="select @error('status') is-invalid @enderror">
                        <option value="{{ App\Models\Job::STATUS_PUBLISHED }}" {{ old('status', App\Models\Job::STATUS_PUBLISHED) == App\Models\Job::STATUS_PUBLISHED ? 'selected' : '' }}>公開</option>
                        <option value="{{ App\Models\Job::STATUS_DRAFT }}" {{ old('status') == App\Models\Job::STATUS_DRAFT ? 'selected' : '' }}>下書き</option>
                        <option value="{{ App\Models\Job::STATUS_STOPPED }}" {{ old('status') == App\Models\Job::STATUS_STOPPED ? 'selected' : '' }}>停止</option>
                    </select>
                    <div class="help">公開のみフリーランス側の案件一覧に表示される想定</div>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="btn-row flex flex-col md:flex-row gap-3 md:gap-4">
                    <a class="btn btn-secondary w-full md:flex-1" href="{{ route('company.jobs.index') }}">キャンセル</a>
                    <button class="btn btn-primary w-full md:flex-1" type="submit">登録</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        (function () {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.getElementById('userDropdownToggle');
            const menu = document.getElementById('userDropdownMenu');
            if (!dropdown || !toggle || !menu) return;
            const open = () => { dropdown.classList.add('is-open'); toggle.setAttribute('aria-expanded', 'true'); };
            const close = () => { dropdown.classList.remove('is-open'); toggle.setAttribute('aria-expanded', 'false'); };
            const isOpen = () => dropdown.classList.contains('is-open');
            toggle.addEventListener('click', (e) => { e.stopPropagation(); isOpen() ? close() : open(); });
            document.addEventListener('click', (e) => { if (!dropdown.contains(e.target)) close(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
    </script>

    <script>
        (function () {
            const pad2 = (n) => String(n).padStart(2, '0');
            const isValidDate = (y, m, d) => {
                const dt = new Date(y, m - 1, d);
                return dt.getFullYear() === y && (dt.getMonth() + 1) === m && dt.getDate() === d;
            };
            const lastDayOfMonth = (y, m) => new Date(y, m, 0).getDate();

            const initYmdWidget = (widgetId) => {
                const widget = document.getElementById(widgetId);
                if (!widget) return;
                const hiddenInputId = widget.dataset.hiddenInputId;
                const hiddenInput = document.getElementById(hiddenInputId);
                if (!hiddenInput) return;

                const yearSelect = widget.querySelector('[data-role="year"]');
                const monthSelect = widget.querySelector('[data-role="month"]');
                const daySelect = widget.querySelector('[data-role="day"]');
                if (!yearSelect || !monthSelect || !daySelect) return;

                const currentYear = new Date().getFullYear();
                const minYear = currentYear - 10;
                const maxYear = currentYear + 10;

                for (let y = maxYear; y >= minYear; y--) {
                    const opt = document.createElement('option');
                    opt.value = String(y);
                    opt.textContent = String(y);
                    yearSelect.appendChild(opt);
                }

                for (let m = 1; m <= 12; m++) {
                    const opt = document.createElement('option');
                    opt.value = String(m);
                    opt.textContent = String(m);
                    monthSelect.appendChild(opt);
                }

                const fillDays = (selectedDay = '') => {
                    const y = parseInt(yearSelect.value, 10);
                    const m = parseInt(monthSelect.value, 10);
                    daySelect.innerHTML = '<option value="">日</option>';
                    if (!y || !m) return;

                    const maxDay = lastDayOfMonth(y, m);
                    for (let d = 1; d <= maxDay; d++) {
                        const opt = document.createElement('option');
                        opt.value = String(d);
                        opt.textContent = String(d);
                        daySelect.appendChild(opt);
                    }

                    if (selectedDay && parseInt(selectedDay, 10) <= maxDay) {
                        daySelect.value = String(parseInt(selectedDay, 10));
                    }
                };

                const syncHiddenInput = () => {
                    const y = parseInt(yearSelect.value, 10);
                    const m = parseInt(monthSelect.value, 10);
                    const d = parseInt(daySelect.value, 10);
                    if (!y || !m || !d || !isValidDate(y, m, d)) {
                        hiddenInput.value = '';
                        return;
                    }
                    hiddenInput.value = `${y}-${pad2(m)}-${pad2(d)}`;
                };

                const setFromIso = (iso) => {
                    const match = (iso || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
                    if (!match) return;

                    const y = parseInt(match[1], 10);
                    const m = parseInt(match[2], 10);
                    const d = parseInt(match[3], 10);
                    if (!isValidDate(y, m, d)) return;

                    yearSelect.value = String(y);
                    monthSelect.value = String(m);
                    fillDays(String(d));
                    daySelect.value = String(d);
                };

                if (hiddenInput.value) {
                    setFromIso(hiddenInput.value);
                }

                yearSelect.addEventListener('change', () => {
                    fillDays(daySelect.value);
                    syncHiddenInput();
                });
                monthSelect.addEventListener('change', () => {
                    fillDays(daySelect.value);
                    syncHiddenInput();
                });
                daySelect.addEventListener('change', syncHiddenInput);
            };

            initYmdWidget('work_start_date_widget');
            initYmdWidget('publish_end_date_widget');
        })();
    </script>

    <script>
        // 必要スキル（複数フォーム入力 → hidden にカンマ結合）
        (function () {
            const container = document.getElementById('required-skills-container');
            const hidden = document.getElementById('required_skills_text');
            const addBtn = document.getElementById('required-skills-add-row');
            const removeBtn = document.getElementById('required-skills-remove-row');
            if (!container || !hidden || !addBtn || !removeBtn) return;

            const MIN_ROWS = 2;
            const COLS = 4;

            const splitSkills = (text) => {
                return String(text || '')
                    .split(/[\n,、]+/)
                    .map((s) => s.trim())
                    .filter(Boolean);
            };

            const getRows = () => Array.from(container.querySelectorAll('.skills-row-4'));
            const getInputs = () => Array.from(container.querySelectorAll('input.skill-input'));

            const syncHidden = () => {
                const values = getInputs()
                    .map((i) => (i.value || '').trim())
                    .filter(Boolean);
                hidden.value = values.join(', ');
            };

            const bindInputs = (root) => {
                const inputs = Array.from((root || container).querySelectorAll('input.skill-input'));
                inputs.forEach((input) => {
                    if (input.hasAttribute('data-listener-added')) return;
                    input.addEventListener('input', syncHidden);
                    input.addEventListener('change', syncHidden);
                    input.setAttribute('data-listener-added', 'true');
                });
            };

            const updateRemoveState = () => {
                removeBtn.disabled = getRows().length <= MIN_ROWS;
            };

            const createRow = () => {
                const row = document.createElement('div');
                row.className = 'skills-row-4';
                for (let i = 0; i < COLS; i++) {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'input skill-input';
                    input.maxLength = 500;
                    row.appendChild(input);
                }
                return row;
            };

            const ensureCapacity = (needed) => {
                while (getInputs().length < needed) {
                    const row = createRow();
                    container.appendChild(row);
                    bindInputs(row);
                }
                updateRemoveState();
            };

            addBtn.addEventListener('click', () => {
                const row = createRow();
                container.appendChild(row);
                bindInputs(row);
                updateRemoveState();
            });

            removeBtn.addEventListener('click', () => {
                const rows = getRows();
                if (rows.length <= MIN_ROWS) return;
                rows[rows.length - 1].remove();
                syncHidden();
                updateRemoveState();
            });

            // 初期値反映
            const initialSkills = splitSkills(hidden.value);
            ensureCapacity(Math.max(MIN_ROWS * COLS, initialSkills.length));
            const inputs = getInputs();
            initialSkills.forEach((skill, idx) => {
                if (inputs[idx]) inputs[idx].value = skill;
            });
            bindInputs(container);
            syncHidden();
            updateRemoveState();

            // submit 時も同期（念のため）
            const form = hidden.closest('form');
            if (form) {
                form.addEventListener('submit', syncHidden);
            }
        })();
    </script>
</body>
</html>
