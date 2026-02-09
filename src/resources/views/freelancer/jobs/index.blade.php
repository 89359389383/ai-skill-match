<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリーランス案件一覧 - AITECH</title>
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
    </style>
    <style>
        :root {
            --header-height: 104px;       /* 80px * 1.3 */
            --header-height-mobile: 91px; /* 70px * 1.3 */
            --header-height-sm: 96px;         /* sm */
            --header-height-md: 104px;        /* md */
            --header-height-lg: 112px;        /* lg */
            --header-height-xl: 120px;        /* xl */
            --header-height-current: var(--header-height-mobile);
            --header-padding-x: 1rem;
            --container-max-width: 1600px;
            --main-padding: 0.5rem;
            --sidebar-width: 320px;
            --sidebar-gap: 3rem;
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

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 97.5%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        /* Main Layout */
        .main-content {
            display: flex;
            max-width: var(--container-max-width);
            margin: 0 auto;
            padding: var(--main-padding);
            gap: var(--sidebar-gap);
        }
        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            position: sticky;
            top: calc(var(--header-height) + 1.5rem);
            align-self: flex-start;
        }
        .content-area { flex: 1; min-width: 0; }

        .search-section {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
            margin-bottom: 2rem;
        }
        .search-section h3 {
            font-size: 1.1rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            color: #24292e;
            letter-spacing: -0.01em;
        }
        .search-group { margin-bottom: 1.5rem; }
        .search-group label {
            display: block;
            font-weight: 900;
            margin-bottom: 0.75rem;
            color: #586069;
            font-size: 0.9rem;
        }
        .search-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .search-input:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
            background-color: white;
        }
        .radio-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .radio-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .price-range {
            display: grid;
            gap: 0.75rem;
        }
        .price-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .price-row-label {
            font-weight: 800;
            color: #586069;
            font-size: 0.9rem;
            min-width: 40px;
        }
        .price-input {
            flex: 1;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
            max-width: 140px;
        }
        .price-input:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
            background-color: white;
        }
        .price-row-unit {
            font-weight: 800;
            color: #586069;
            font-size: 0.9rem;
            min-width: 60px;
        }
        .price-help {
            color: #6a737d;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        .search-btn {
            width: 100%;
            background-color: #0366d6;
            color: white;
            border: none;
            padding: 0.875rem 1rem;
            border-radius: 10px;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 0.95rem;
        }
        .search-btn:hover { background-color: #0256cc; box-shadow: 0 2px 8px rgba(3, 102, 214, 0.3); }

        .jobs-grid {
            display: grid;
            gap: 2rem;
        }
        .job-card {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
            border: 1px solid #e1e4e8;
            position: relative;
            overflow: hidden;
        }
        .job-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08);
        }
        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .job-title {
            font-size: 24px;
            font-weight: 700;
            color: #0060ff;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .company-name {
            color: #586069;
            font-size: 18px;
            font-weight: 500;
        }
        .job-description {
            color: #586069;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            font-size: 1rem;
        }
        .job-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
            max-width: 600px;
        }
        .detail-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
            background-color: #f6f8fa;
            border-radius: 10px;
        }
        .detail-label {
            font-size: 1rem;
            color: #6a737d;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
            white-space: nowrap;
        }
        .detail-value {
            font-weight: 900;
            color: #24292e;
            font-size: 1.1rem;
            white-space: nowrap;
        }
        .skills-section {
            margin-bottom: 1.75rem;
        }
        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .skill-tag {
            background-color: #f1f8ff;
            color: #0366d6;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 600;
            border: 1px solid #c8e1ff;
        }
        .job-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid #e1e4e8;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            font-weight: 800;
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
        .btn-secondary { background-color: #586069; color: white; font-size: 20px; padding: 15px 60px; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }
        .btn-primary { background-color: #0366d6; color: white; font-size: 20px; padding: 15px 60px; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }
        .btn-success { background-color: #28a745; color: white; font-size: 20px; padding: 15px 60px; }
        .btn-success:hover { background-color: #218838; transform: translateY(-1px); }
        .btn-success:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Pagination */
        .pagination {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
        }
        .pagination-list {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            align-items: center;
        }
        .pagination-link {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            color: #0366d6;
            font-weight: 600;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        .pagination-link:hover {
            background-color: #f6f8fa;
            border-color: #e1e4e8;
        }
        .pagination-link.is-active {
            background-color: #0366d6;
            color: white;
            border-color: #0366d6;
        }
        .pagination-link.is-disabled {
            color: #6a737d;
            cursor: not-allowed;
            opacity: 0.5;
        }
        .pagination-link.is-disabled:hover {
            background-color: transparent;
            border-color: transparent;
        }
        .pagination-ellipsis {
            padding: 0.5rem;
            color: #6a737d;
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <header class="header" role="banner">
        <div class="header-content">
            <div class="header-left">
                <div class="logo" aria-hidden="true">
                    <div class="logo-text">複業AI</div>
                </div>
            </div>

            <nav class="nav-links" role="navigation" aria-label="フリーランスナビゲーション">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link active">案件一覧</a>
                @php
                    $appUnread = ($unreadApplicationCount ?? 0);
                    $scoutUnread = ($unreadScoutCount ?? 0);
                @endphp
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ $appUnread > 0 ? 'has-badge' : '' }}">
                    応募した案件
                    @if($appUnread > 0)
                        <span class="badge" aria-live="polite">{{ $appUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($scoutUnread > 0)
                        <span class="badge" aria-hidden="false">{{ $scoutUnread }}</span>
                    @endif
                </a>
            </nav>

            <div class="header-right" role="region" aria-label="ユーザー">
                <button
                    class="nav-toggle"
                    id="mobileNavToggle"
                    type="button"
                    aria-label="メニューを開く"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    aria-controls="mobileNav"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 6h18"></path>
                        <path d="M3 12h18"></path>
                        <path d="M3 18h18"></path>
                    </svg>
                </button>

                <div class="user-menu">
                    <div class="dropdown" id="userDropdown">
                        <button class="user-avatar" id="userDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="userDropdownMenu">
                            @if(isset($freelancer) && $freelancer && $freelancer->icon_path)
                                <img src="{{ asset('storage/' . $freelancer->icon_path) }}" alt="プロフィール画像" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ $userInitial ?? 'U' }}
                            @endif
                        </button>
                        <div class="dropdown-content" id="userDropdownMenu" role="menu" aria-label="ユーザーメニュー">
                            <a href="{{ route('freelancer.profile.settings') }}" class="dropdown-item" role="menuitem">プロフィール設定</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('auth.logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item" role="menuitem" style="width: 100%; text-align: left; background: none; border: none; padding: 0.875rem 1.25rem; color: #586069; cursor: pointer; font-size: inherit; font-family: inherit;">ログアウト</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mobile-nav" id="mobileNav" role="menu" aria-label="モバイルナビゲーション">
            <div class="mobile-nav-inner">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link active">案件一覧</a>
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ $appUnread > 0 ? 'has-badge' : '' }}">
                    応募した案件
                    @if($appUnread > 0)
                        <span class="badge" aria-live="polite">{{ $appUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($scoutUnread > 0)
                        <span class="badge" aria-hidden="false">{{ $scoutUnread }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-10 flex flex-col lg:flex-row gap-6 lg:gap-8">
        <!-- Sidebar -->
        <aside class="sidebar w-full lg:w-80 lg:sticky lg:top-[calc(var(--header-height)+1.5rem)]">
            <div class="search-section">
                <h3>検索条件</h3>
                <form method="GET" action="{{ route('freelancer.jobs.index') }}" id="searchForm">
                <div class="search-group">
                    <label for="keyword">キーワード</label>
                    <input type="text" id="keyword" name="keyword" class="search-input" placeholder="案件名 / 会社名 / スキル など" value="{{ old('keyword', $keyword ?? '') }}">
                </div>

                <div class="search-group">
                    <label>報酬</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" id="priceTypeMonthly" name="price-type" class="radio-input" value="monthly" checked>
                            <span>単価</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" id="priceTypeHourly" name="price-type" class="radio-input" value="hourly">
                            <span>時給</span>
                        </label>
                    </div>
                    <div class="price-range">
                        <div class="price-row">
                            <span class="price-row-label">下限</span>
                            <input type="number" id="priceMin" class="price-input" placeholder="例: 50" min="0" step="1" inputmode="numeric">
                            <span class="price-row-unit priceUnit">万円</span>
                        </div>
                        <div class="price-row">
                            <span class="price-row-label">上限</span>
                            <input type="number" id="priceMax" class="price-input" placeholder="例: 70" min="0" step="1" inputmode="numeric">
                            <span class="price-row-unit priceUnit">万円</span>
                        </div>
                    </div>
                    <div class="price-help" id="priceHelp">例: 50 〜 70（万円）</div>
                </div>

                <button type="submit" class="search-btn">検索</button>
                </form>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="content-area flex-1 min-w-0">
        <div class="jobs-grid grid grid-cols-1 gap-5 lg:gap-6">
                @forelse($jobs as $job)
                    @php
                        $isApplied = in_array($job->id, $appliedJobIds ?? []);
                        $rewardText = '';
                        if ($job->reward_type === 'monthly') {
                            $rewardText = ($job->min_rate / 10000) . '〜' . ($job->max_rate / 10000) . '万円';
                        } else {
                            $rewardText = number_format($job->min_rate) . '〜' . number_format($job->max_rate) . '円/時';
                        }
                        $skills = $job->required_skills_text ? explode(',', $job->required_skills_text) : [];
                    @endphp
                    <div class="job-card p-5 md:p-7">
                        <div class="job-header">
                            <div>
                                <h2 class="job-title">{{ $job->title }}</h2>
                                <div class="company-name">{{ $job->company->name }}</div>
                            </div>
                        </div>

                        <p class="job-description">{{ $job->description }}</p>

                        <div class="job-details grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                            <div class="detail-item">
                                <div class="detail-label">稼働時間</div>
                                <div class="detail-value">{{ $job->work_time_text }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">報酬</div>
                                <div class="detail-value">{{ $rewardText }}</div>
                            </div>
                        </div>

                        @if(count($skills) > 0)
                        <div class="skills-section">
                            <div class="skills">
                                @foreach($skills as $skill)
                                    <span class="skill-tag">{{ trim($skill) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="job-actions flex flex-col md:flex-row gap-3 border-t border-slate-200 pt-4 mt-5">
                            <a href="{{ route('freelancer.jobs.show', $job->id) }}" class="btn btn-secondary w-full md:flex-1">詳細</a>
                            @if($isApplied)
                                @php
                                    $thread = $threadMap[$job->id] ?? null;
                                @endphp
                                @if($thread)
                                    <a href="{{ route('freelancer.threads.show', $thread['id']) }}" class="btn btn-success w-full md:flex-1">応募済み（チャットを開く）</a>
                                @else
                                    <button class="btn btn-success w-full md:flex-1" disabled>応募済み</button>
                                @endif
                            @else
                                <a href="{{ route('freelancer.jobs.apply.create', $job->id) }}" class="btn btn-primary w-full md:flex-1">応募</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="job-card">
                        <p class="job-description" style="text-align: center; padding: 2rem;">
                            該当する案件が見つかりませんでした。
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($jobs->hasPages())
            <nav class="pagination" aria-label="ページネーション">
                <ul class="pagination-list">
                    @if($jobs->onFirstPage())
                        <li><span class="pagination-link is-disabled" aria-disabled="true">前へ</span></li>
                    @else
                        <li><a class="pagination-link" href="{{ $jobs->previousPageUrl() }}">前へ</a></li>
                    @endif

                    @php
                        $currentPage = $jobs->currentPage();
                        $lastPage = $jobs->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp

                    @if($startPage > 1)
                        <li><a class="pagination-link" href="{{ $jobs->url(1) }}">1</a></li>
                        @if($startPage > 2)
                            <li><span class="pagination-ellipsis" aria-hidden="true">…</span></li>
                        @endif
                    @endif

                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <li><span class="pagination-link is-active" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a class="pagination-link" href="{{ $jobs->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <li><span class="pagination-ellipsis" aria-hidden="true">…</span></li>
                        @endif
                        <li><a class="pagination-link" href="{{ $jobs->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif

                    @if($jobs->hasMorePages())
                        <li><a class="pagination-link" href="{{ $jobs->nextPageUrl() }}">次へ</a></li>
                    @else
                        <li><span class="pagination-link is-disabled" aria-disabled="true">次へ</span></li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </main>

    <script>
        (function () {
            const header = document.querySelector('header.header');
            const toggle = document.getElementById('mobileNavToggle');
            const mobileNav = document.getElementById('mobileNav');
            if (!header || !toggle || !mobileNav) return;

            const OPEN_CLASS = 'is-mobile-nav-open';
            const isOpen = () => header.classList.contains(OPEN_CLASS);

            const open = () => {
                header.classList.add(OPEN_CLASS);
                toggle.setAttribute('aria-expanded', 'true');
            };

            const close = () => {
                header.classList.remove(OPEN_CLASS);
                toggle.setAttribute('aria-expanded', 'false');
            };

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                if (isOpen()) close();
                else open();
            });

            document.addEventListener('click', (e) => {
                if (!header.contains(e.target)) close();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) close();
            });
        })();
    </script>
    <script>
        (function () {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.getElementById('userDropdownToggle');
            const menu = document.getElementById('userDropdownMenu');
            if (!dropdown || !toggle || !menu) return;

            const open = () => {
                dropdown.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            };

            const close = () => {
                dropdown.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            };

            const isOpen = () => dropdown.classList.contains('is-open');

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                if (isOpen()) close();
                else open();
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) close();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') close();
            });
        })();
    </script>
    <script>
        (function () {
            const monthly = document.getElementById('priceTypeMonthly');
            const hourly = document.getElementById('priceTypeHourly');
            const minInput = document.getElementById('priceMin');
            const maxInput = document.getElementById('priceMax');
            const unitEls = document.querySelectorAll('.priceUnit');
            const help = document.getElementById('priceHelp');
            if (!monthly || !hourly || !minInput || !maxInput || unitEls.length === 0 || !help) return;

            const applyType = (type) => {
                const isMonthly = type === 'monthly';

                unitEls.forEach((el) => {
                    el.textContent = isMonthly ? '万円' : '円/時';
                });

                minInput.placeholder = isMonthly ? '例: 50' : '例: 2500';
                maxInput.placeholder = isMonthly ? '例: 70' : '例: 4000';

                minInput.step = isMonthly ? '1' : '10';
                maxInput.step = isMonthly ? '1' : '10';

                help.textContent = isMonthly
                    ? '例: 50 〜 70（万円）'
                    : '例: 2500 〜 4000（円/時）';
            };

            const onChange = () => applyType(monthly.checked ? 'monthly' : 'hourly');
            monthly.addEventListener('change', onChange);
            hourly.addEventListener('change', onChange);

            // 初期反映
            onChange();
        })();
    </script>
</body>
</html>