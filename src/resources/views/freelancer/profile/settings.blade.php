<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>設定 - AITECH</title>
    <link rel="icon" href="{{ asset('aifavicon.png') }}">
    @auth('freelancer')
        @include('partials.freelancer-header-style')
    @endauth
    {{-- ヘッダーに必要なスタイルのみをここに記載 --}}
    <style>
        /* Header (企業側と同じレスポンシブ構造: 640 / 768 / 1024 / 1280) */
        .header {
            background-color: #ffffff;
            border-bottom: 1px solid #e1e4e8;
            padding: 0 var(--header-padding-x, 1rem);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            min-height: var(--header-height-current, 91px);
        }

        .header-content {
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr auto; /* mobile: ロゴ / 右側 */
            align-items: center;
            gap: 0.5rem;
            height: var(--header-height-current, 91px);
            position: relative;
            min-width: 0;
            padding: 0.25rem 0; /* 縦余白 */
        }

        /* md以上: ロゴ / 中央ナビ / 右側 */
        @media (min-width: 768px) {
            .header-content { grid-template-columns: auto 1fr auto; gap: 1rem; }
        }

        /* lg: 間隔を広げる */
        @media (min-width: 1024px) {
            .header-content { gap: 1.5rem; padding: 0.5rem 0; }
        }

        .header-left { display: flex; align-items: center; gap: 0.75rem; min-width: 0; }
        .header-right { display: flex; align-items: center; justify-content: flex-end; min-width: 0; gap: 0.75rem; }

        /* ロゴ */
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

        /* Mobile nav toggle (<=768pxで表示) */
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

        /* Desktop nav (>=768pxで表示) */
        .nav-links {
            display: none; /* mobile: hidden (use hamburger) */
            align-items: center;
            justify-content: center;
            flex-wrap: nowrap;
            min-width: 0;
            overflow: hidden;
            gap: 1.25rem;
        }
        @media (min-width: 640px) { .nav-links { display: none; } }
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

        /* Mobile nav menu */
        .mobile-nav {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-bottom: 1px solid #e1e4e8;
            box-shadow: 0 16px 40px rgba(0,0,0,0.10);
            padding: 0.75rem var(--header-padding-x, 1rem);
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

        /* User menu / Dropdown */
        .user-menu { display: flex; align-items: center; position: static; transform: none; }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: none;
            padding: 0;
            appearance: none;
        }
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
        .dropdown-divider { height: 1px; background-color: #e1e4e8; margin: 0.5rem 0; }
    </style>
    <style>
        /* 元の settings.css をそのまま保持します（ヘッダーは共通レイアウトに移動） */
        :root {
            --header-height: 72px;
            --header-height-mobile: 72px;
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

        /* Breakpoint: md (>=768px) */
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

        :root {
            --header-height: 72px;
            --header-height-mobile: 72px;
            --container-max-width: 1600px;
            --main-padding: 3rem;
            --sidebar-width: 320px;
            --sidebar-gap: 3rem;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 97.5%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        .main-content {
            display: flex;
            max-width: 1150px;
            margin: 0 auto;
            padding: var(--main-padding);
            /* public-header が fixed のため、ヘッダー分だけ本文を下げる */
            padding-top: calc(4rem + 1.5rem);
            gap: var(--sidebar-gap);
        }
        @media (min-width: 768px) {
            .main-content { padding-top: calc(4rem + 2.5rem); }
        }
        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            /* デフォルトでは固定しない（モバイル/タブレットで通常フローにする） */
            position: static;
            top: auto;
            align-self: flex-start;
        }

        /* 大きい画面（lg相当）でのみ固定する */
        @media (min-width: 1024px) {
            .sidebar {
                position: sticky;
                top: calc(var(--header-height) + 1.5rem);
            }
        }
        .content-area { flex: 1; min-width: 0; }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: #24292e;
            letter-spacing: -0.025em;
        }
        .page-subtitle {
            color: #6a737d;
            font-size: 1rem;
            margin-bottom: 2.25rem;
        }

        .panel {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
            margin-bottom: 2rem;
        }
        .panel-title {
            font-size: 1.1rem;
            font-weight: 900;
            margin-bottom: 1.25rem;
            color: #24292e;
            letter-spacing: -0.01em;
        }

        .profile-card {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
        }
        .profile-head {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }
        .big-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .big-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .name {
            font-size: 1.25rem;
            font-weight: 900;
            color: #24292e;
            margin-bottom: 0.25rem;
        }
        .headline {
            color: #6a737d;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .skill-tag {
            background-color: #f1f8ff;
            color: #0366d6;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #c8e1ff;
        }
        .divider {
            height: 1px;
            background-color: #e1e4e8;
            margin: 1rem 0;
        }
        .kv {
            display: grid;
            gap: 0.75rem;
        }
        .k {
            font-weight: 800;
            color: #586069;
            font-size: 0.9rem;
        }
        .v {
            color: #24292e;
            font-weight: 600;
        }
        .help {
            color: #6a737d;
            font-size: 0.9rem;
            font-style: italic;
        }

        .form {
            display: grid;
            gap: 1.5rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            /* 左カラム(表示名)のエラーメッセージで行の高さが増えても、
               右カラム(職種)が同じ高さに引き伸ばされないようにする */
            align-items: start;
        }
        .row {
            display: grid;
            gap: 0.5rem;
        }
        .label {
            font-weight: 900;
            color: #586069;
            font-size: 0.9rem;
        }
        .input, .textarea, .select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .input:focus, .textarea:focus, .select:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
            background-color: white;
        }
        .input.is-invalid, .textarea.is-invalid {
            border-color: #d73a49;
        }
        .textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.6;
        }
        .file-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px dashed #e1e4e8;
            border-radius: 10px;
            background-color: #fafbfc;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .file-input:hover {
            border-color: #0366d6;
            background-color: #f6f8fa;
        }
        .file-input:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
        }
        .error-message {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 800;
            color: #dc2626;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid #e1e4e8;
            flex-wrap: wrap;
        }
        .btn {
            padding: 15px 60px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            font-size: 20px;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }
        .btn-primary { background-color: #0366d6; color: white; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }
        .btn-secondary { background-color: #586069; color: white; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }

        .btn-outline {
            background-color: transparent;
            color: #0366d6;
            border: 2px solid #0366d6;
            padding: 8px 14px;
            font-size: 16px;
        }
        .btn-outline:hover {
            background-color: #f1f8ff;
            color: #0256cc;
            border-color: #0256cc;
        }

        .skills-container {
            display: grid;
            gap: 0.75rem;
        }
        .skill-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }
        @media (max-width: 768px) {
            .skill-input-row {
                grid-template-columns: 1fr;
            }
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.public-header')

    <main class="main-content max-w-7xl mx-auto px-4 md:px-6 lg:px-8 pb-6 md:pb-10 flex flex-col lg:flex-row gap-6 lg:gap-8">
        <aside class="sidebar w-full lg:w-80 lg:sticky lg:top-[calc(var(--header-height)+1.5rem)]" aria-label="設定メニュー">
            <div class="panel profile-card" >
                <div class="panel-title">プレビュー</div>
                <div class="profile-head">
                    <div class="big-avatar" id="preview-avatar">
                        @if($freelancer && $freelancer->icon_path)
                            <img src="{{ asset('storage/' . $freelancer->icon_path) }}" alt="プロフィール画像">
                        @else
                            {{ mb_substr($freelancer->display_name ?? ($user->email ?? 'U'), 0, 1) }}
                        @endif
                    </div>
                    <div style="min-width:0;">
                        <div class="name" id="preview-name">{{ $freelancer->display_name ?? '未入力' }}</div>
                        <div class="headline" id="preview-headline">{{ $freelancer->job_title ?? '未入力' }}</div>
                    </div>
                </div>
                <div class="skills" id="preview-skills" aria-label="スキル">
                    @if($freelancer && $freelancer->customSkills)
                        @foreach($freelancer->customSkills as $skill)
                            <span class="skill-tag">{{ $skill->name }}</span>
                        @endforeach
                    @endif
                </div>
                <div class="divider"></div>
                <div class="kv" aria-label="条件">
                    <div class="k">希望単価</div>
                    <div class="v" id="preview-rate">
                        @if($freelancer && ($freelancer->min_rate || $freelancer->max_rate))
                            @if($freelancer->min_rate && $freelancer->max_rate)
                                {{ $freelancer->min_rate }}〜{{ $freelancer->max_rate }}万円
                            @else
                                {{ $freelancer->min_rate ?? $freelancer->max_rate }}万円
                            @endif
                        @else
                            未設定
                        @endif
                    </div>
                    <div class="k">稼働</div>
                    <div class="v" id="preview-hours">
                        @if($freelancer && ($freelancer->min_hours_per_week || $freelancer->max_hours_per_week))
                            @if($freelancer->min_hours_per_week && $freelancer->max_hours_per_week)
                                週{{ $freelancer->min_hours_per_week }}〜{{ $freelancer->max_hours_per_week }}h
                            @else
                                週{{ $freelancer->min_hours_per_week ?? $freelancer->max_hours_per_week }}h
                            @endif
                        @else
                            未設定
                        @endif
                    </div>
                    <div class="k">日</div>
                    <div class="v" id="preview-days">
                        @if($freelancer && ($freelancer->hours_per_day || $freelancer->days_per_week))
                            @if($freelancer->hours_per_day && $freelancer->days_per_week)
                                {{ $freelancer->hours_per_day }}h/day・{{ $freelancer->days_per_week }}日/week
                            @else
                                {{ $freelancer->hours_per_day ?? '' }}{{ $freelancer->hours_per_day ? 'h/day' : '' }}{{ $freelancer->days_per_week ?? '' }}{{ $freelancer->days_per_week ? '日/week' : '' }}
                            @endif
                        @else
                            未設定
                        @endif
                    </div>
                </div>
                <p class="help" style="margin-top:1rem;">プロフィールが充実しているほどスカウトが届きやすくなります。</p>
            </div>
        </aside>

        <div class="content-area flex-1 min-w-0">
            <h1 class="page-title">プロフィール設定</h1>
            @include('partials.error-panel')
            <p class="page-subtitle">プロフィール（メール/パスワード以外）を編集します。</p>

            <section class="panel" aria-label="プロフィール編集">
                <div class="panel-title">プロフィール</div>
                @if(session('success'))
                    <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
                        {{ session('error') }}
                    </div>
                @endif
                <form class="form" action="{{ route('freelancer.profile.settings.update') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="grid-2 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div class="row">
                            <div class="label">表示名</div>
                            <input class="input @error('display_name') is-invalid @enderror" id="display_name" name="display_name" type="text" maxlength="255" value="{{ old('display_name', $freelancer->display_name ?? '') }}">
                            @error('display_name')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="label">職種（自由入力）</div>
                            <input class="input @error('job_title') is-invalid @enderror" id="job_title" name="job_title" type="text" maxlength="255" value="{{ old('job_title', $freelancer->job_title ?? '') }}">
                            @error('job_title')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">自己紹介</div>
                        <textarea class="textarea @error('bio') is-invalid @enderror" id="bio" name="bio" maxlength="5000" placeholder="あなたの経験や得意分野について教えてください">{{ old('bio', $freelancer->bio ?? '') }}</textarea>
                        @error('bio')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="label">担当業務・得意業務（自由入力テキスト）</div>
                        <textarea class="textarea @error('work_style_text') is-invalid @enderror" id="work_style_text" name="work_style_text">{{ old('work_style_text', $freelancer->work_style_text ?? '') }}</textarea>
                        @error('work_style_text')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    @php
                        // 対応業務 / 得意業種 / 資格: 複数入力（min=4, max=16 / 各カテゴリ）
                        $servicesOfferedRaw = old('services_offered', $freelancer->services_offered ?? '');
                        $industrySpecialtiesRaw = old('industry_specialties', $freelancer->industry_specialties ?? '');
                        $certificationsRaw = old('certifications', $freelancer->certifications ?? '');

                        $splitByCommaLike = function ($raw) {
                            $raw = (string) $raw;
                            if ($raw === '') return [];
                            $parts = preg_split('/[、,]/u', $raw, -1, PREG_SPLIT_NO_EMPTY);
                            return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
                        };

                        $splitByNewline = function ($raw) {
                            $raw = (string) $raw;
                            if ($raw === '') return [];
                            $parts = preg_split("/\r\n|\n|\r/u", $raw, -1, PREG_SPLIT_NO_EMPTY);
                            return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
                        };

                        $servicesOfferedValues = $splitByCommaLike($servicesOfferedRaw);
                        $industrySpecialtiesValues = $splitByCommaLike($industrySpecialtiesRaw);
                        $certificationsValues = $splitByNewline($certificationsRaw);

                        $maxCount = max(4, count($servicesOfferedValues), count($industrySpecialtiesValues), count($certificationsValues));
                        $styleRows = min(4, max(1, (int) ceil($maxCount / 4)));

                        $servicesOfferedValues = array_pad(array_slice($servicesOfferedValues, 0, 16), $styleRows * 4, '');
                        $industrySpecialtiesValues = array_pad(array_slice($industrySpecialtiesValues, 0, 16), $styleRows * 4, '');
                        $certificationsValues = array_pad(array_slice($certificationsValues, 0, 16), $styleRows * 4, '');
                    @endphp

                    <div class="row">
                        <div class="label">対応業務（任意）</div>
                        <input type="hidden" id="services_offered_hidden" name="services_offered" value="{{ old('services_offered', $freelancer->services_offered ?? '') }}">
                        <div id="services_offered_items_container">
                            @for($row = 0; $row < $styleRows; $row++)
                                <div class="style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @for($col = 0; $col < 4; $col++)
                                        @php $idx = $row * 4 + $col; @endphp
                                        <input
                                            class="input services-offered-item"
                                            name="services_offered_items[]"
                                            type="text"
                                            value="{{ $servicesOfferedValues[$idx] ?? '' }}"
                                            placeholder="{{ $idx === 0 ? '例: Webアプリ開発' : '' }}"
                                        >
                                    @endfor
                                </div>
                            @endfor
                        </div>
                        @error('services_offered')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button type="button" class="btn btn-outline" id="add-services-row">追加する</button>
                            <button type="button" class="btn btn-outline" id="remove-services-row" aria-label="対応業務の入力行を減らす">×</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">得意業界（任意）</div>
                        <input type="hidden" id="industry_specialties_hidden" name="industry_specialties" value="{{ old('industry_specialties', $freelancer->industry_specialties ?? '') }}">
                        <div id="industry_specialties_items_container">
                            @for($row = 0; $row < $styleRows; $row++)
                                <div class="style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @for($col = 0; $col < 4; $col++)
                                        @php $idx = $row * 4 + $col; @endphp
                                        <input
                                            class="input industry-specialties-item"
                                            name="industry_specialties_items[]"
                                            type="text"
                                            value="{{ $industrySpecialtiesValues[$idx] ?? '' }}"
                                            placeholder="{{ $idx === 0 ? '例: IT・Web' : '' }}"
                                        >
                                    @endfor
                                </div>
                            @endfor
                        </div>
                        @error('industry_specialties')
                        <span class="error-message">{{ $message }}</span>
                        @enderror

                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button type="button" class="btn btn-outline" id="add-industry-row">追加する</button>
                            <button type="button" class="btn btn-outline" id="remove-industry-row" aria-label="得意業界の入力行を減らす">×</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">在住都道府県（任意）</div>
                        <input class="input @error('prefecture') is-invalid @enderror" id="prefecture" name="prefecture" type="text" value="{{ old('prefecture', $freelancer->prefecture ?? '') }}" placeholder="例: 東京都">
                        @error('prefecture')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="label">資格（任意）</div>
                        <input type="hidden" id="certifications_hidden" name="certifications" value="{{ old('certifications', $freelancer->certifications ?? '') }}">
                        <div id="certifications_items_container">
                            @for($row = 0; $row < $styleRows; $row++)
                                <div class="style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @for($col = 0; $col < 4; $col++)
                                        @php $idx = $row * 4 + $col; @endphp
                                        <input
                                            class="input certifications-item"
                                            name="certifications_items[]"
                                            type="text"
                                            value="{{ $certificationsValues[$idx] ?? '' }}"
                                            placeholder="{{ $idx === 0 ? '例: 基本情報技術者' : '' }}"
                                        >
                                    @endfor
                                </div>
                            @endfor
                        </div>
                        @error('certifications')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button type="button" class="btn btn-outline" id="add-certifications-row">追加する</button>
                            <button type="button" class="btn btn-outline" id="remove-certifications-row" aria-label="資格の入力行を減らす">×</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">スキル（1つ以上推奨・複数入力）</div>
                        <div class="help">複数入力できます。</div>
                        @php
                            $skillsInvalid = $errors->has('custom_skills');
                            $customSkillValues = old('custom_skills');
                            if (!is_array($customSkillValues)) {
                                $customSkillValues = [];
                                if (isset($freelancer) && $freelancer && $freelancer->customSkills) {
                                    $customSkillValues = $freelancer->customSkills->pluck('name')->toArray();
                                }
                            }
                            $minSlots = 4;
                            if (count($customSkillValues) < $minSlots) {
                                $customSkillValues = array_pad($customSkillValues, $minSlots, null);
                            }
                        @endphp

                        <div class="skills-container" id="skills-container">
                            @for($i = 0; $i < count($customSkillValues); $i += 2)
                                <div class="skill-input-row">
                                    <input class="input skill-input {{ $skillsInvalid ? 'is-invalid' : '' }}" name="custom_skills[]" type="text" value="{{ $customSkillValues[$i] ?? '' }}" placeholder="{{ $i === 0 ? '例: n8n' : '' }}">
                                    <input class="input skill-input" name="custom_skills[]" type="text" value="{{ $customSkillValues[$i + 1] ?? '' }}" placeholder="">
                                </div>
                            @endfor
                        </div>
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                            <button type="button" class="btn btn-outline" id="add-skill-btn">追加する</button>
                            <button type="button" class="btn btn-outline" id="remove-skill-btn" aria-label="スキル入力欄を減らす">×</button>
                        </div>
                        @error('custom_skills')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                        @error('custom_skills.*')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="label">希望単価（万円/月）</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input class="input @error('min_rate') is-invalid @enderror" id="min_rate" name="min_rate" type="number" placeholder="下限" value="{{ old('min_rate', $freelancer->min_rate ?? '') }}" min="0" max="100000000">
                                @error('min_rate')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <input class="input @error('max_rate') is-invalid @enderror" id="max_rate" name="max_rate" type="number" placeholder="上限" value="{{ old('max_rate', $freelancer->max_rate ?? '') }}" min="0" max="100000000">
                                @error('max_rate')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">稼働時間（時間/週）</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input class="input @error('min_hours_per_week') is-invalid @enderror" id="min_hours_per_week" name="min_hours_per_week" type="number" placeholder="下限" value="{{ old('min_hours_per_week', $freelancer->min_hours_per_week ?? '') }}" min="0" max="168">
                                @error('min_hours_per_week')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <input class="input @error('max_hours_per_week') is-invalid @enderror" id="max_hours_per_week" name="max_hours_per_week" type="number" placeholder="上限" value="{{ old('max_hours_per_week', $freelancer->max_hours_per_week ?? '') }}" min="0" max="168">
                                @error('max_hours_per_week')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="row">
                            <div class="label">1日の稼働時間</div>
                            <input class="input @error('hours_per_day') is-invalid @enderror" id="hours_per_day" name="hours_per_day" type="number" value="{{ old('hours_per_day', $freelancer->hours_per_day ?? '') }}" min="0" max="24" placeholder="例: 8">
                            @error('hours_per_day')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="label">週の稼働日数</div>
                            <input class="input @error('days_per_week') is-invalid @enderror" id="days_per_week" name="days_per_week" type="number" value="{{ old('days_per_week', $freelancer->days_per_week ?? '') }}" min="0" max="7" placeholder="例: 5">
                            @error('days_per_week')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="label">プロフィール画像</div>
                        <input type="file" class="file-input @error('icon') is-invalid @enderror" id="icon" name="icon" accept="image/*">
                        @if($freelancer && $freelancer->icon_path)
                            <div style="margin-top:0.75rem; display:flex; align-items:center; gap:0.75rem;">
                                <img
                                    src="{{ asset('storage/' . $freelancer->icon_path) }}"
                                    alt="現在のプロフィール画像"
                                    style="width:72px; height:72px; border-radius:50%; object-fit:cover; border:1px solid #e5e7eb;"
                                >
                                <p style="margin:0; font-size: 0.9rem; color: #6a737d;">変更する場合のみ選択してください。</p>
                            </div>
                        @endif
                        @error('icon')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="divider"></div>
                    <div class="panel-title" style="margin-bottom:1rem;">連絡先（任意）</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div class="row">
                            <div class="label">電話番号</div>
                            <input class="input @error('phone') is-invalid @enderror" id="phone" name="phone" type="tel" value="{{ old('phone', $freelancer->phone ?? '') }}" placeholder="例: 090-1234-5678">
                            @error('phone')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="label">LINE（友達追加URL）</div>
                            <input class="input @error('line_id') is-invalid @enderror" id="line_id" name="line_id" type="text" value="{{ old('line_id', $freelancer->line_id ?? '') }}" placeholder="LINE友達追加URL（例: https://line.me/ti/p/~xxxx または https://lin.ee/xxxx）">
                            @error('line_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="label">Twitter（プロフィールURL）</div>
                        <input class="input @error('twitter_url') is-invalid @enderror" id="twitter_url" name="twitter_url" type="url" value="{{ old('twitter_url', $freelancer->twitter_url ?? '') }}" placeholder="TwitterプロフィールURL（例: https://twitter.com/username または https://x.com/username）">
                        @error('twitter_url')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="actions flex flex-col md:flex-row justify-end gap-3 md:gap-4 border-t border-slate-200 pt-4">
                        <button class="btn btn-primary w-full md:w-auto" type="submit">更新</button>
                    </div>
                </form>
            </section>
        </div>
    </main>

    <script>
        // リアルタイムプレビュー機能
        (function () {
            const displayName = document.getElementById('display_name');
            const jobTitle = document.getElementById('job_title');
            const bio = document.getElementById('bio');
            const minRate = document.getElementById('min_rate');
            const maxRate = document.getElementById('max_rate');
            const minHours = document.getElementById('min_hours_per_week');
            const maxHours = document.getElementById('max_hours_per_week');
            const hoursPerDay = document.getElementById('hours_per_day');
            const daysPerWeek = document.getElementById('days_per_week');
            const addSkillBtn = document.getElementById('add-skill-btn');
            const removeSkillBtn = document.getElementById('remove-skill-btn');
            const skillsContainer = document.getElementById('skills-container');
            const MIN_SKILL_SLOTS = 4;

            const previewName = document.getElementById('preview-name');
            const previewHeadline = document.getElementById('preview-headline');
            const previewSkills = document.getElementById('preview-skills');
            const previewRate = document.getElementById('preview-rate');
            const previewHours = document.getElementById('preview-hours');
            const previewDays = document.getElementById('preview-days');

            function getSkillSlotCount() {
                return document.querySelectorAll('input[name="custom_skills[]"]').length;
            }

            function syncRemoveButton() {
                if (!removeSkillBtn) return;
                const canRemove = getSkillSlotCount() > MIN_SKILL_SLOTS;
                removeSkillBtn.disabled = !canRemove;
                removeSkillBtn.setAttribute('aria-disabled', String(!canRemove));
            }

            function updatePreview() {
                if (displayName && previewName) {
                    previewName.textContent = displayName.value || '未入力';
                }
                if (jobTitle && previewHeadline) {
                    previewHeadline.textContent = jobTitle.value || '未入力';
                }
                if (previewSkills) {
                    previewSkills.innerHTML = '';
                    const skillInputs = document.querySelectorAll('input[name="custom_skills[]"]');
                    skillInputs.forEach(input => {
                        const value = (input.value || '').trim();
                        if (!value) return;
                        const tag = document.createElement('span');
                        tag.className = 'skill-tag';
                        tag.textContent = value;
                        previewSkills.appendChild(tag);
                    });
                }
                if (minRate && maxRate && previewRate) {
                    const min = minRate.value;
                    const max = maxRate.value;
                    if (min && max) {
                        previewRate.textContent = min + '〜' + max + '万円';
                    } else if (min || max) {
                        previewRate.textContent = (min || max) + '万円';
                    } else {
                        previewRate.textContent = '未設定';
                    }
                }
                if (minHours && maxHours && previewHours) {
                    const min = minHours.value;
                    const max = maxHours.value;
                    if (min && max) {
                        previewHours.textContent = '週' + min + '〜' + max + 'h';
                    } else if (min || max) {
                        previewHours.textContent = '週' + (min || max) + 'h';
                    } else {
                        previewHours.textContent = '未設定';
                    }
                }
                if (hoursPerDay && daysPerWeek && previewDays) {
                    const hours = hoursPerDay.value;
                    const days = daysPerWeek.value;
                    if (hours && days) {
                        previewDays.textContent = hours + 'h/day・' + days + '日/week';
                    } else if (hours || days) {
                        previewDays.textContent = (hours ? hours + 'h/day' : '') + (days ? days + '日/week' : '');
                    } else {
                        previewDays.textContent = '未設定';
                    }
                }
            }

            // 数値入力を即座に min/max の範囲へクランプ（不正値が入った瞬間に補正）
            function clampNumericInput(el) {
                if (!el) return;
                if (el.type !== 'number') return;

                const minAttr = el.getAttribute('min');
                const maxAttr = el.getAttribute('max');
                const min = minAttr === null || minAttr === '' ? null : Number(minAttr);
                const max = maxAttr === null || maxAttr === '' ? null : Number(maxAttr);

                const raw = el.value;
                if (raw === '' || raw === null || raw === undefined) return;

                const val = Number(raw);
                if (Number.isNaN(val)) return;

                let next = val;
                if (min !== null && next < min) next = min;
                if (max !== null && next > max) next = max;

                if (next !== val) el.value = String(next);
            }

            function clampTextInput(el) {
                if (!el) return;
                const maxLenAttr = el.getAttribute('maxlength');
                if (!maxLenAttr) return;
                const maxLen = Number(maxLenAttr);
                if (!maxLen || maxLen <= 0) return;
                if (el.value && el.value.length > maxLen) {
                    el.value = el.value.slice(0, maxLen);
                }
            }

            // イベントリスナーを追加
            [displayName, jobTitle, minRate, maxRate, minHours, maxHours, hoursPerDay, daysPerWeek].forEach(el => {
                if (!el) return;
                el.addEventListener('input', function () {
                    clampNumericInput(el);
                    clampTextInput(el);
                    updatePreview();
                });
            });

            // bio も maxlength 超過を即時クランプ
            if (bio) {
                bio.addEventListener('input', function () {
                    clampTextInput(bio);
                });
            }

            // スキル入力欄追加機能（corporate/settings と同様）
            if (addSkillBtn && skillsContainer) {
                addSkillBtn.addEventListener('click', function() {
                    const lastRow = skillsContainer.lastElementChild;
                    const inputsInLastRow = lastRow ? lastRow.querySelectorAll('.skill-input') : [];

                    if (lastRow && inputsInLastRow.length < 2) {
                        const newInput = document.createElement('input');
                        newInput.className = 'input skill-input';
                        newInput.name = 'custom_skills[]';
                        newInput.type = 'text';
                        newInput.placeholder = '例: スキル名';
                        lastRow.appendChild(newInput);
                        newInput.addEventListener('input', updatePreview);
                        newInput.addEventListener('change', updatePreview);
                    } else {
                        const newRow = document.createElement('div');
                        newRow.className = 'skill-input-row';

                        const newInput = document.createElement('input');
                        newInput.className = 'input skill-input';
                        newInput.name = 'custom_skills[]';
                        newInput.type = 'text';
                        newInput.placeholder = '例: スキル名';
                        newRow.appendChild(newInput);
                        skillsContainer.appendChild(newRow);

                        newInput.addEventListener('input', updatePreview);
                        newInput.addEventListener('change', updatePreview);
                    }
                    syncRemoveButton();
                });
            }

            // スキル入力欄削除（×ボタンは1つ、最小4つ）
            if (removeSkillBtn && skillsContainer) {
                removeSkillBtn.addEventListener('click', function () {
                    if (getSkillSlotCount() <= MIN_SKILL_SLOTS) {
                        syncRemoveButton();
                        return;
                    }

                    const inputs = skillsContainer.querySelectorAll('input[name="custom_skills[]"]');
                    const lastInput = inputs[inputs.length - 1];
                    if (!lastInput) return;

                    const row = lastInput.closest('.skill-input-row');
                    lastInput.remove();

                    if (row) {
                        const remaining = row.querySelectorAll('input[name="custom_skills[]"]').length;
                        if (remaining === 0) row.remove();
                    }

                    updatePreview();
                    syncRemoveButton();
                });
            }

            // 既存スキル入力にもプレビュー更新を紐づけ
            const skillInputs = document.querySelectorAll('input[name="custom_skills[]"]');
            skillInputs.forEach(input => {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            });

            // 初期表示
            updatePreview();
            syncRemoveButton();
        })();

        // プロフィール画像プレビュー（選択した瞬間に差し替え）
        (function () {
            const iconInput = document.getElementById('icon');
            const previewAvatar = document.getElementById('preview-avatar');
            if (!iconInput || !previewAvatar) return;

            const defaultPreviewHTML = previewAvatar.innerHTML;

            iconInput.addEventListener('change', function () {
                const file = iconInput.files && iconInput.files[0] ? iconInput.files[0] : null;
                if (!file || !file.type || !file.type.startsWith('image/')) {
                    previewAvatar.innerHTML = defaultPreviewHTML;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const dataUrl = String(e.target && e.target.result ? e.target.result : '');
                    if (!dataUrl) return;
                    previewAvatar.innerHTML = '<img src="' + dataUrl + '" alt="プロフィール画像">';
                };
                reader.readAsDataURL(file);
            });
        })();

        // 対応業務 / 得意業種 / 資格: 複数入力（min=4, max=16 / 各項目独立）
        (function () {
            const servicesContainer = document.getElementById('services_offered_items_container');
            const industryContainer = document.getElementById('industry_specialties_items_container');
            const certificationsContainer = document.getElementById('certifications_items_container');

            const hiddenServices = document.getElementById('services_offered_hidden');
            const hiddenIndustry = document.getElementById('industry_specialties_hidden');
            const hiddenCertifications = document.getElementById('certifications_hidden');

            const addServicesBtn = document.getElementById('add-services-row');
            const removeServicesBtn = document.getElementById('remove-services-row');
            const addIndustryBtn = document.getElementById('add-industry-row');
            const removeIndustryBtn = document.getElementById('remove-industry-row');
            const addCertBtn = document.getElementById('add-certifications-row');
            const removeCertBtn = document.getElementById('remove-certifications-row');

            const MAX_ROWS = 4;
            const MIN_ROWS = 1;

            if (!servicesContainer || !industryContainer || !certificationsContainer || !hiddenServices || !hiddenIndustry || !hiddenCertifications) {
                return;
            }

            function buildRow({ inputClass, inputName, placeholder }) {
                const row = document.createElement('div');
                row.className = 'style-input-row grid grid-cols-2 sm:grid-cols-4 gap-3';
                for (let col = 0; col < 4; col++) {
                    const input = document.createElement('input');
                    input.className = 'input ' + inputClass;
                    input.name = inputName;
                    input.type = 'text';
                    input.placeholder = placeholder;
                    row.appendChild(input);
                }
                return row;
            }

            function syncHiddenFields() {
                const services = Array.from(servicesContainer.querySelectorAll('.services-offered-item'))
                    .map(el => (el.value || '').trim())
                    .filter(Boolean);
                const industries = Array.from(industryContainer.querySelectorAll('.industry-specialties-item'))
                    .map(el => (el.value || '').trim())
                    .filter(Boolean);
                const certifications = Array.from(certificationsContainer.querySelectorAll('.certifications-item'))
                    .map(el => (el.value || '').trim())
                    .filter(Boolean);

                hiddenServices.value = services.join(',');
                hiddenIndustry.value = industries.join(',');
                hiddenCertifications.value = certifications.join('\n');
            }

            function setRowButtonState(container, addBtn, removeBtn) {
                const rowCount = container.querySelectorAll('.style-input-row').length;
                if (addBtn) addBtn.disabled = rowCount >= MAX_ROWS;
                if (removeBtn) removeBtn.disabled = rowCount <= MIN_ROWS;
            }

            function bindControls({ container, addBtn, removeBtn, rowTemplate }) {
                if (!container) return;

                if (addBtn) {
                    addBtn.addEventListener('click', function () {
                        const rowCount = container.querySelectorAll('.style-input-row').length;
                        if (rowCount >= MAX_ROWS) return;
                        container.appendChild(buildRow(rowTemplate));
                        setRowButtonState(container, addBtn, removeBtn);
                        syncHiddenFields();
                    });
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', function () {
                        const rows = container.querySelectorAll('.style-input-row');
                        rows.forEach((row, idx) => {
                            if (idx >= MIN_ROWS) row.remove();
                        });
                        setRowButtonState(container, addBtn, removeBtn);
                        syncHiddenFields();
                    });
                }

                container.addEventListener('input', syncHiddenFields);
            }

            bindControls({
                container: servicesContainer,
                addBtn: addServicesBtn,
                removeBtn: removeServicesBtn,
                rowTemplate: { inputClass: 'services-offered-item', inputName: 'services_offered_items[]', placeholder: '例: Webアプリ開発' }
            });
            bindControls({
                container: industryContainer,
                addBtn: addIndustryBtn,
                removeBtn: removeIndustryBtn,
                rowTemplate: { inputClass: 'industry-specialties-item', inputName: 'industry_specialties_items[]', placeholder: '例: IT・Web' }
            });
            bindControls({
                container: certificationsContainer,
                addBtn: addCertBtn,
                removeBtn: removeCertBtn,
                rowTemplate: { inputClass: 'certifications-item', inputName: 'certifications_items[]', placeholder: '例: 基本情報技術者' }
            });

            setRowButtonState(servicesContainer, addServicesBtn, removeServicesBtn);
            setRowButtonState(industryContainer, addIndustryBtn, removeIndustryBtn);
            setRowButtonState(certificationsContainer, addCertBtn, removeCertBtn);
            syncHiddenFields();

            const form = document.querySelector('form[action="{{ route('freelancer.profile.settings.update') }}"]');
            if (form) {
                form.addEventListener('submit', function () {
                    syncHiddenFields();
                });
            }
        })();
    </script>
</body>
</html>