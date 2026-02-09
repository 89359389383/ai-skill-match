<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募する - AITECH</title>
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
        .user-avatar:hover { transform: scale(1.08); box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
        .user-avatar:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.25), 0 2px 8px rgba(0,0,0,0.1); }

        /* Main Layout */
        .main-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 3rem;
        }
        .content-area { width: 100%; }
        .page-title {
            font-size: 2rem;
            font-weight: 700;
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
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 1.25rem;
            color: #24292e;
            letter-spacing: -0.01em;
        }

        .job-summary {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .summary-line {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.25rem;
        }
        .summary-label {
            color: #6a737d;
            font-weight: 700;
            font-size: 20px;
            flex-shrink: 0;
        }
        .summary-value {
            color: #24292e;
            font-weight: 700;
            font-size: 20px;
        }
        .summary-separator {
            color: #6a737d;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0 0.25rem;
        }
        .kv {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 0.75rem 1rem;
            align-items: start;
        }
        .k { color: #6a737d; font-weight: 700; font-size: 0.9rem; }
        .v { color: #24292e; font-weight: 700; font-size: 0.95rem; }

        .skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }
        .skill-tag {
            background-color: #f1f8ff;
            color: #0366d6;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid #c8e1ff;
        }

        .form {
            display: grid;
            gap: 1.25rem;
        }
        .form-row { display: grid; gap: 0.6rem; }
        .label {
            font-weight: 800;
            color: #586069;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .required {
            font-size: 0.75rem;
            font-weight: 900;
            color: white;
            background: #d73a49;
            border-radius: 999px;
            padding: 0.15rem 0.55rem;
            letter-spacing: 0.02em;
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
        .textarea { min-height: 160px; resize: vertical; line-height: 1.6; }
        .input:focus, .textarea:focus, .select:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
            background-color: white;
        }
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
        .help {
            color: #6a737d;
            font-size: 0.85rem;
            line-height: 1.5;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
            padding-top: 1rem;
            border-top: 1px solid #e1e4e8;
            flex-wrap: wrap;
        }
        .actions .btn {
            flex: 1;
            min-width: 0;
        }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
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
        .btn-secondary {
            background-color: #586069;
            color: white;
            font-size: 20px;
            padding: 15px 60px;
        }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }
        .btn-primary {
            background-color: #0366d6;
            color: white;
            font-size: 20px;
            padding: 15px 60px;
        }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }

        /* Dropdown Menu */
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
            margin: 0 0.25rem;
            white-space: nowrap;
        }
        .dropdown-item:hover { background-color: #f6f8fa; color: #24292e; }
        .dropdown-divider { height: 1px; background-color: #e1e4e8; margin: 0.5rem 0; }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Header -->
    <header class="header" role="banner">
        <div class="header-content">
            <div class="header-left">
                <div class="logo" aria-hidden="true">
                    <div class="logo-text">複業AI</div>
                </div>
            </div>

            <nav class="nav-links" role="navigation" aria-label="フリーランスナビゲーション">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link">案件一覧</a>
                @php
                    $totalUnreadCount = ($unreadApplicationCount ?? 0) + ($unreadScoutCount ?? 0);
                @endphp
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ $totalUnreadCount > 0 ? 'has-badge' : '' }} active">
                    応募した案件
                    @if($totalUnreadCount > 0)
                        <span class="badge" aria-live="polite">{{ $totalUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ $totalUnreadCount > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($totalUnreadCount > 0)
                        <span class="badge" aria-hidden="false">{{ $totalUnreadCount }}</span>
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
                        <button class="user-avatar" id="userDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="userDropdownMenu">{{ $userInitial ?? 'U' }}</button>
                        <div class="dropdown-content" id="userDropdownMenu" role="menu" aria-label="ユーザーメニュー">
                            <a href="{{ route('freelancer.profile.settings') }}" class="dropdown-item" role="menuitem">プロフィール設定</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('auth.logout') }}" class="dropdown-item" role="menuitem" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mobile-nav" id="mobileNav" role="menu" aria-label="モバイルナビゲーション">
            <div class="mobile-nav-inner">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link">案件一覧</a>
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ $totalUnreadCount > 0 ? 'has-badge' : '' }} active">
                    応募した案件
                    @if($totalUnreadCount > 0)
                        <span class="badge" aria-live="polite">{{ $totalUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ $totalUnreadCount > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($totalUnreadCount > 0)
                        <span class="badge" aria-hidden="false">{{ $totalUnreadCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    <main class="main-content max-w-5xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-10">
        <div class="content-area">
            <h1 class="page-title">応募する</h1>
            @include('partials.error-panel')
            <p class="page-subtitle">応募メッセージを入力して応募を送信します。送信後はチャット画面へ遷移します。</p>

            <!-- 応募先案件 -->
            <div class="panel">
                <div class="panel-title">応募先案件</div>
                <div class="job-summary">
                    <div class="summary-line">
                        <span class="summary-label">会社名：</span>
                        <span class="summary-value">{{ $job->company->name }}</span>
                    </div>
                    <div class="summary-line">
                        <span class="summary-label">求人名：</span>
                        <span class="summary-value">{{ $job->title }}</span>
                    </div>
                    <div class="summary-line">
                        <span class="summary-label">報酬：</span>
                        <span class="summary-value">
                            @php
                                $rewardText = '';
                                if ($job->reward_type === 'monthly') {
                                    $rewardText = ($job->min_rate / 10000) . '〜' . ($job->max_rate / 10000) . '万円';
                                } else {
                                    $rewardText = number_format($job->min_rate) . '〜' . number_format($job->max_rate) . '円/時';
                                }
                            @endphp
                            {{ $rewardText }}
                        </span>
                    </div>
                    <div class="summary-line">
                        <span class="summary-label">想定稼働時間／期間：</span>
                        <span class="summary-value">{{ $job->work_time_text }}</span>
                    </div>
                    @if($job->required_skills_text)
                    <div class="summary-line">
                        <span class="summary-label">必要スキル：</span>
                        <div class="summary-value skills" aria-label="必要スキル">
                            @php
                                $skills = explode(',', $job->required_skills_text);
                            @endphp
                            @foreach($skills as $skill)
                                <span class="skill-tag">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- 応募内容 -->
            <div class="panel">
                <div class="panel-title">応募内容</div>
                <form class="form" action="{{ route('freelancer.jobs.apply.store', $job) }}" method="post">
                    @csrf
                    <div class="form-row">
                        <label class="label" for="message">応募メッセージ <span class="required">必須</span></label>
                        <textarea id="message" class="textarea @error('message') is-invalid @enderror" name="message" placeholder="例) 要件の◯◯に対して、Laravel + Vueでの実装経験があります。稼働は週25h、開始は1月上旬から可能です。実績: https://...">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="actions flex flex-col md:flex-row gap-3 md:gap-4">
                        <a class="btn btn-secondary w-full md:flex-1" href="{{ route('freelancer.jobs.show', $job) }}" role="button">戻る</a>
                        <button class="btn btn-primary w-full md:flex-1" type="submit">応募を送信</button>
                    </div>
                </form>
            </div>

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
</body>
</html>