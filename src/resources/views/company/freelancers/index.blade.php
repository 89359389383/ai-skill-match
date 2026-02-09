<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリーランス一覧（企業）- AITECH</title>
    @include('partials.company-header-style')
    <style>
        :root {
            --header-height: 104px;           /* md 基本高さ */
            --header-height-mobile: 91px;     /* xs / mobile */
            --header-height-sm: 96px;         /* sm */
            --header-height-md: 104px;        /* md */
            --header-height-lg: 112px;        /* lg */
            --header-height-xl: 120px;        /* xl */
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
            flex: 0 0 auto;
        }
        /* avatar size responsive */
        @media (min-width: 640px) { .user-avatar { width: 40px; height: 40px; } }
        @media (min-width: 768px) { .user-avatar { width: 44px; height: 44px; } }
        @media (min-width: 1024px) { .user-avatar { width: 48px; height: 48px; } }
        @media (min-width: 1280px) { .user-avatar { width: 52px; height: 52px; } }
        .user-avatar:hover { transform: scale(1.08); box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
        .user-avatar:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.25), 0 2px 8px rgba(0,0,0,0.1); }

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

        /* Layout */
        .main-content {
            display: flex;
            max-width: 1600px;
            margin: 0 auto;
            padding: 3rem;
            gap: 3rem;
        }
        .sidebar:not(.right) {
            width: 320px;
            flex-shrink: 0;
            /* 左サイドバー（デスクトップは追従、モバイルは通常フロー） */
            position: sticky;
            top: calc(var(--header-height-current) + 1.5rem);
            align-self: flex-start;
            z-index: 50;
        }
        .sidebar.right {
            width: 380px;
            flex-shrink: 0;
            /* 右サイドバー（デスクトップは追従、モバイルは通常フロー） */
            position: sticky;
            top: calc(var(--header-height-current) + 1.5rem);
            align-self: flex-start;
            z-index: 50;
        }
        .content-area {
            flex: 1;
            min-width: 0;
            margin-left: 0;
            margin-right: 0;
        }

        /* Panels / Inputs */
        .panel {
            background-color: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
            margin-bottom: 2rem;
            min-width: 0;
            overflow: hidden;
        }
        .panel h3 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #24292e;
            letter-spacing: -0.01em;
        }
        .field { margin-bottom: 1.25rem; }
        .field label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #586069;
            font-size: 0.9rem;
        }
        .input, .textarea, .select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .textarea { min-height: 140px; resize: vertical; }
        .input:focus, .textarea:focus, .select:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
            background-color: white;
        }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }
        .btn-primary { background-color: #0366d6; color: white; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }
        .search-btn {
            width: 100%;
            background-color: #0366d6;
            color: white;
            border: none;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 16px;
        }
        .search-btn:hover {
            background-color: #0256cc;
            box-shadow: 0 2px 8px rgba(3, 102, 214, 0.3);
        }
        .btn-secondary { background-color: #586069; color: white; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }

        /* Cards */
        .page-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: #24292e;
            letter-spacing: -0.025em;
        }
        .list { display: grid; gap: 1.5rem; }
        .card {
            background-color: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
            border: 1px solid #e1e4e8;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            min-width: 0;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08); }
        .card.is-selected { outline: 3px solid rgba(3, 102, 214, 0.2); border-color: rgba(3,102,214,0.35); }
        .card-header {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            display: flex;
            gap: 0.5rem;
            z-index: 5;
        }
        .card-scout-btn {
            padding: 0.45rem 0.65rem;
            margin-top: 8px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.12s ease;
            white-space: nowrap;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .row { display: flex; gap: 1rem; align-items: flex-start; min-width: 0; }
        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            flex: 0 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        }
        .name { font-size: 1.1rem; font-weight: 800; line-height: 1.2; margin-bottom: 0.25rem; word-wrap: break-word; overflow-wrap: break-word; }
        .sub { color: #586069; font-weight: 600; font-size: 0.85rem; word-wrap: break-word; overflow-wrap: break-word; }
        .desc { color: #586069; margin-top: 0.75rem; line-height: 1.6; word-wrap: break-word; overflow-wrap: break-word; }
        .tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.85rem; }
        .tag { background-color: #f1f8ff; color: #0366d6; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 700; border: 1px solid #c8e1ff; }
        .meta {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .meta-item {
            padding: 0.85rem;
            background-color: #f6f8fa;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
            min-width: 0;
            overflow: hidden;
        }
        .meta-label { font-size: 15px; color: #6a737d; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; flex-shrink: 0; }
        .meta-value {
            font-weight: 800;
            color: #24292e;
            min-width: 0;
            text-align: right;
            /* 複数行を許容して稼働情報などを切らさない */
            white-space: normal;
            overflow: visible;
        }

        /* Right detail */
        #detailPanel {
            /* パネル内でスクロール可能にする */
            max-height: calc(100vh - var(--header-height-current) - 3rem);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        #detailPanel h3 {
            /* タイトルは固定 */
            flex-shrink: 0;
        }
        #detailContent {
            /* コンテンツ部分がスクロール可能 */
            flex: 1;
            min-height: 0;
        }
        #detailEmpty {
            /* 空の状態も固定 */
            flex-shrink: 0;
        }
        /* スクロールバーのスタイリング */
        #detailPanel::-webkit-scrollbar {
            width: 8px;
        }
        #detailPanel::-webkit-scrollbar-track {
            background: #f6f8fa;
            border-radius: 4px;
        }
        #detailPanel::-webkit-scrollbar-thumb {
            background: #c6cbd1;
            border-radius: 4px;
        }
        #detailPanel::-webkit-scrollbar-thumb:hover {
            background: #959da5;
        }
        .detail-title { font-size: 0.95rem; font-weight: 800; margin-top: 0.75rem; margin-bottom: 0.5rem; }
        .detail-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .link { color: #0366d6; text-decoration: none; font-weight: 800; word-break: break-all; overflow-wrap: break-word; display: block; }
        .link:hover { text-decoration: underline; }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.company-header')

    <main class="main-content max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-10 flex flex-col lg:flex-row gap-6 lg:gap-8">
        <aside class="sidebar w-full lg:w-80">
            <div class="panel">
                <h3>検索条件</h3>
                <form method="GET" action="{{ route('company.freelancers.index') }}">
                    <div class="field">
                        <label for="keyword">フリーワード</label>
                        <input id="keyword" name="keyword" class="input" type="text" placeholder="職種 / スキル / 自己紹介 / 希望単価 など" value="{{ old('keyword', $keyword ?? '') }}">
                    </div>
                    <button class="search-btn" type="submit">検索</button>
                </form>
            </div>
        </aside>

        <section class="content-area flex-1 min-w-0">
            <h1 class="page-title text-2xl md:text-3xl font-black tracking-tight">フリーランス一覧</h1>
            <div class="list grid grid-cols-1 gap-5" id="freelancerList" aria-label="フリーランス一覧">
                @forelse($freelancers as $index => $freelancer)
                    @php
                        $avatarText = mb_substr($freelancer->display_name, 0, 1);
                        $allSkills = $freelancer->skills->pluck('name')->merge($freelancer->customSkills->pluck('name'));
                        $workHours = $freelancer->min_hours_per_week . '〜' . $freelancer->max_hours_per_week . 'h';
                        $dailyText = null;
                        if (isset($freelancer->hours_per_day) || isset($freelancer->days_per_week)) {
                            $dailyParts = [];
                            if (isset($freelancer->hours_per_day)) { $dailyParts[] = $freelancer->hours_per_day . 'h/day'; }
                            if (isset($freelancer->days_per_week)) { $dailyParts[] = $freelancer->days_per_week . 'day/week'; }
                            $dailyText = implode('・', $dailyParts);
                        }
                        $workStyleText = '週' . $workHours . ($dailyText ? ' ' . $dailyText : '');
                        $minRate = $freelancer->min_rate ?: null; // 0は未設定扱い
                        $maxRate = $freelancer->max_rate ?: null; // 0は未設定扱い
                        if ($minRate !== null && $maxRate !== null) {
                            $rateText = $minRate . '〜' . $maxRate . '万';
                        } elseif ($minRate !== null || $maxRate !== null) {
                            $rateText = ($minRate ?? $maxRate) . '万';
                        } else {
                            $rateText = '未設定';
                        }
                    @endphp
                    @php
                        $currentThreadId = $scoutThreadMap[$freelancer->id] ?? null;
                    @endphp
                    <article class="card {{ $index === 0 ? 'is-selected' : '' }} rounded-2xl bg-white border border-slate-200 shadow-sm p-5 md:p-6 relative overflow-hidden" tabindex="0" role="button" data-id="{{ $freelancer->id }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}">
                        <div class="card-header">
                            @if($currentThreadId)
                                <a class="card-scout-btn btn-secondary" href="{{ route('company.threads.show', ['thread' => $currentThreadId]) }}" aria-label="スカウト済み">スカウト済み</a>
                            @else
                                <a class="card-scout-btn btn-primary" href="{{ route('company.scouts.create', ['freelancer_id' => $freelancer->id]) }}" aria-label="スカウト">スカウト</a>
                            @endif
                        </div>
                        <div class="row">
                            <div class="avatar" aria-hidden="true">{{ $avatarText }}</div>
                            <div style="min-width:0; flex: 1; overflow: hidden;">
                                <div class="name">{{ $freelancer->display_name }}</div>
                                <div class="sub">{{ $freelancer->job_title ?? '' }}</div>
                                @if($allSkills->isNotEmpty())
                                    <div class="tags" aria-label="スキル">
                                        @foreach($allSkills as $skill)
                                            <span class="tag">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="meta" aria-label="ワークスタイル">
                            <div class="meta-item">
                                <div class="meta-label">希望単価</div>
                                <div class="meta-value" title="{{ $rateText }}">{{ $rateText }}</div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">稼働</div>
                                <div class="meta-value" title="{{ $workStyleText }}">
                                    {{ '週' . $workHours }}
                                    @if($dailyText)
                                        <br><span>{{ $dailyText }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-8 md:p-10 text-center text-slate-600">
                        <p class="text-base md:text-lg font-semibold">フリーランスが見つかりませんでした。</p>
                    </div>
                @endforelse
            </div>
            @if($freelancers->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $freelancers->links() }}
                </div>
            @endif
        </section>

        <aside class="sidebar right w-full lg:w-[380px]">
            <div class="panel" id="detailPanel" aria-live="polite">
                <h3>選択中のフリーランス</h3>
                <div id="detailContent" style="display: none;">
                    <div class="row">
                        <div class="avatar" id="dAvatar" aria-hidden="true"></div>
                        <div style="min-width:0; flex: 1; overflow: hidden;">
                            <div class="name" id="dName"></div>
                            <div class="sub" id="dRole"></div>
                        </div>
                    </div>

                    <div class="detail-title">自己紹介</div>
                    <div class="desc" id="dBio"></div>

                    <div class="detail-title">スキル</div>
                    <div class="tags" id="dSkills"></div>

                    <div class="meta" id="dMeta"></div>

                    <div class="detail-title">ポートフォリオ</div>
                    <div class="desc" id="dPortfolio" style="word-break: break-all; overflow-wrap: break-word;"></div>

                    <div class="detail-title">経験企業</div>
                    <div class="desc" id="dExperienceCompanies"></div>

                    <div class="detail-actions">
                        @php
                            $firstFreelancer = $freelancers->first();
                            $firstThreadId = $firstFreelancer ? ($scoutThreadMap[$firstFreelancer->id] ?? null) : null;
                        @endphp
                        @if($firstThreadId)
                            <a class="btn btn-secondary" id="dScoutLink" href="{{ route('company.threads.show', ['thread' => $firstThreadId]) }}">スカウト済み</a>
                        @else
                            <a class="btn btn-primary" id="dScoutLink" href="{{ $firstFreelancer ? route('company.scouts.create', ['freelancer_id' => $firstFreelancer->id]) : '#' }}">スカウト</a>
                        @endif
                    </div>
                </div>
                <div id="detailEmpty" style="text-align: center; padding: 2rem; color: #586069;">
                    <p>フリーランスを選択してください</p>
                </div>
            </div>
        </aside>
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

    @php
        $freelancerDataArray = $freelancers->map(function($freelancer) use ($scoutThreadMap) {
            $allSkills = $freelancer->skills->pluck('name')->merge($freelancer->customSkills->pluck('name'));
            $workHours = $freelancer->min_hours_per_week . '〜' . $freelancer->max_hours_per_week . 'h';

            $minRate = $freelancer->min_rate ?: null; // 0は未設定扱い
            $maxRate = $freelancer->max_rate ?: null; // 0は未設定扱い
            if ($minRate !== null && $maxRate !== null) {
                $rateText = $minRate . '〜' . $maxRate . '万';
            } elseif ($minRate !== null || $maxRate !== null) {
                $rateText = ($minRate ?? $maxRate) . '万';
            } else {
                $rateText = '未設定';
            }

            return [
                'id' => $freelancer->id,
                'avatar' => mb_substr($freelancer->display_name, 0, 1),
                'name' => $freelancer->display_name,
                'role' => $freelancer->job_title ?? '',
                'bio' => $freelancer->bio ?? '',
                'skills' => $allSkills->toArray(),
            'workHours' => $workHours,
            'hoursPerDay' => $freelancer->hours_per_day ?? null,
            'daysPerWeek' => $freelancer->days_per_week ?? null,
            // フリー入力の働き方（プロフィールにある自由入力項目）を返す（存在すれば表示に使う）
            'workStyleText' => $freelancer->work_style ?? $freelancer->work_style_text ?? '',
            // フルリモート希望フラグ（モデル上の複数候補名に対応）
            'prefersFullRemote' => (bool)($freelancer->prefers_full_remote ?? $freelancer->full_remote ?? $freelancer->wants_full_remote ?? false),
                'rateText' => $rateText,
                'portfolios' => $freelancer->portfolios->pluck('url')->toArray(),
                'experienceCompanies' => $freelancer->experience_companies ?? '',
                'threadId' => $scoutThreadMap[$freelancer->id] ?? null,
            ];
        })->values()->toArray();
    @endphp
    <script type="application/json" id="freelancerDataJson">{!! json_encode($freelancerDataArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script>
        (function () {
            const dataEl = document.getElementById('freelancerDataJson');
            const freelancerData = dataEl ? JSON.parse(dataEl.textContent || '[]') : [];

            const list = document.getElementById('freelancerList');
            const detailContent = document.getElementById('detailContent');
            const detailEmpty = document.getElementById('detailEmpty');
            const dAvatar = document.getElementById('dAvatar');
            const dName = document.getElementById('dName');
            const dRole = document.getElementById('dRole');
            const dBio = document.getElementById('dBio');
            const dSkills = document.getElementById('dSkills');
            const dMeta = document.getElementById('dMeta');
            const dPortfolio = document.getElementById('dPortfolio');
            const dExperienceCompanies = document.getElementById('dExperienceCompanies');
            const dScoutLink = document.getElementById('dScoutLink');
            
            if (!list || !detailContent || !detailEmpty || !dAvatar || !dName || !dRole || !dBio || !dSkills || !dMeta || !dPortfolio || !dExperienceCompanies || !dScoutLink) return;

            // データをIDでマッピング
            const dataMap = {};
            freelancerData.forEach(item => {
                dataMap[item.id] = item;
            });

            const render = (id) => {
                const x = dataMap[id];
                if (!x) {
                    detailContent.style.display = 'none';
                    detailEmpty.style.display = 'block';
                    return;
                }
                
                detailContent.style.display = 'block';
                detailEmpty.style.display = 'none';
                
                dAvatar.textContent = x.avatar;
                dName.textContent = x.name;
                dRole.textContent = x.role;
                dBio.textContent = x.bio || '（未設定）';
                dSkills.innerHTML = x.skills.length > 0 
                    ? x.skills.map(s => `<span class="tag">${s}</span>`).join('')
                    : '<span style="color: #586069;">（未設定）</span>';
                const dailyPart = ((x.hoursPerDay || x.daysPerWeek) ? ((x.hoursPerDay ? `${x.hoursPerDay}h/day` : '') + (x.hoursPerDay && x.daysPerWeek ? '・' : '') + (x.daysPerWeek ? `${x.daysPerWeek}day/week` : '')) : '');
                const weekLine = `週${x.workHours}`;
                const dailyLine = dailyPart;

                // サイドバー：メタ情報表示
                let parts = [];

                // フリー入力の働き方があれば表示（その下にメタを出す）
                if (x.workStyleText && x.workStyleText.toString().trim()) {
                    parts.push(`<div class="detail-title">働き方</div><div class="desc" id="dWorkStyle">${String(x.workStyleText).replace(/\n/g,'<br>')}</div>`);
                }

                // 希望単価（上）
                parts.push(`
                    <div class="meta-item">
                        <div class="meta-label">希望単価</div>
                        <div class="meta-value" title="${String(x.rateText ?? '').replaceAll('"','&quot;')}">${x.rateText}</div>
                    </div>
                `);

                // 稼働（週行・その下に日別行）
                parts.push(`
                    <div class="meta-item">
                        <div class="meta-label">稼働</div>
                        <div class="meta-value" title="${(weekLine + (dailyLine ? ' ' + dailyLine : '')).replaceAll('"','&quot;')}">
                            ${weekLine}${dailyLine ? '<br><span>' + dailyLine + '</span>' : ''}
                        </div>
                    </div>
                `);

                dMeta.innerHTML = parts.join('');
                
                if (x.portfolios && x.portfolios.length > 0) {
                    dPortfolio.innerHTML = x.portfolios.map(url =>
                        `<a class="link" href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`
                    ).join('<br>');
                } else {
                    dPortfolio.innerHTML = '<span style="color: #586069;">（未設定）</span>';
                }

                if (x.experienceCompanies && x.experienceCompanies.trim()) {
                    dExperienceCompanies.innerHTML = x.experienceCompanies.replace(/\n/g, '<br>');
                } else {
                    dExperienceCompanies.innerHTML = '<span style="color: #586069;">（未設定）</span>';
                }
                
                // スカウト済みかどうかでボタンの表示とリンクを変更
                if (x.threadId) {
                    // スカウト済みの場合
                    dScoutLink.textContent = 'スカウト済み';
                    dScoutLink.href = '{{ route("company.threads.show", ["thread" => ":threadId"]) }}'.replace(':threadId', x.threadId);
                    dScoutLink.classList.remove('btn-primary');
                    dScoutLink.classList.add('btn-secondary');
                } else {
                    // スカウト未済の場合
                    dScoutLink.textContent = 'スカウト';
                    dScoutLink.href = '{{ route("company.scouts.create") }}?freelancer_id=' + id;
                    dScoutLink.classList.remove('btn-secondary');
                    dScoutLink.classList.add('btn-primary');
                }
            };

            const selectCard = (card) => {
                list.querySelectorAll('.card').forEach((el) => {
                    el.classList.remove('is-selected');
                    el.setAttribute('aria-pressed', 'false');
                });
                card.classList.add('is-selected');
                card.setAttribute('aria-pressed', 'true');
                render(parseInt(card.getAttribute('data-id')));
            };

            // 初期表示：最初のカードを選択
            const firstCard = list.querySelector('.card');
            if (firstCard) {
                selectCard(firstCard);
            }

            list.addEventListener('click', (e) => {
                // スカウトボタンがクリックされた場合はカード選択をしない
                if (e.target.closest('.card-scout-btn')) return;
                const card = e.target.closest('.card');
                if (!card) return;
                selectCard(card);
            });
            list.addEventListener('keydown', (e) => {
                if (e.key !== 'Enter' && e.key !== ' ') return;
                // スカウトボタンにフォーカスがある場合はカード選択をしない
                if (e.target.closest('.card-scout-btn')) return;
                const card = e.target.closest('.card');
                if (!card) return;
                e.preventDefault();
                selectCard(card);
            });
        })();
    </script>
</body>
</html>