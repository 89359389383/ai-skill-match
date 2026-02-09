<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スカウト一覧（企業）- AITECH</title>
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

        .main-content { max-width: 1000px; margin: 0 auto; padding: 3rem; }
        .page-title { font-size: 2rem; font-weight: 900; margin-bottom: 1.5rem; letter-spacing: -0.025em; }
        .topbar { display: flex; justify-content: space-between; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1.5rem; }
        .list { display: grid; gap: 1.5rem; }
        .card {
            background-color: white;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
            border: 1px solid #e1e4e8;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08); }
        .row { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; flex-wrap: wrap; }
        .left { display: flex; gap: 0.85rem; align-items: center; }
        .avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 900;
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
            flex: 0 0 auto;
        }
        .name { font-size: 1.25rem; font-weight: 900; margin-bottom: 0.15rem; }
        .sub { color: #586069; font-weight: 700; }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-weight: 900;
            font-size: 0.85rem;
            border: 1px solid #e1e4e8;
            background: #fafbfc;
            white-space: nowrap;
        }
        .pill.unread { background: #fff5f5; border-color: #ffccd2; color: #b31d28; }
        .desc { color: #586069; margin-top: 0.75rem; line-height: 1.65; }
        .actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e1e4e8; flex-wrap: wrap; }
        .btn {
            padding: 0.875rem 1.75rem; border-radius: 10px; font-weight: 900;
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.15s ease; cursor: pointer; border: none; font-size: 0.95rem; white-space: nowrap;
        }
        .btn-primary { background-color: #0366d6; color: #fff; font-size: 20px; padding: 15px 60px; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3,102,214,0.3); }
        .btn-secondary { background-color: #586069; color: #fff; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.company-header')

    <main class="main-content max-w-5xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-10">
        <div class="topbar flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4">
            <h1 class="page-title text-2xl md:text-3xl font-black tracking-tight">スカウト一覧</h1>
        </div>

        <div class="list grid grid-cols-1 gap-5 lg:gap-6">
            @forelse($threads as $thread)
                @php
                    $freelancer = $thread->freelancer;
                    $avatarText = mb_substr($freelancer->display_name ?? '未', 0, 1);
                    $latestMessage = $thread->messages->first();
                    $scout = $thread->scout ?? null;
                    
                    // 表示するメッセージを決定
                    $displayMessage = '';
                    if ($latestMessage) {
                        $displayMessage = '直近メッセージ: 「' . mb_substr($latestMessage->body, 0, 50) . ($latestMessage->body > 50 ? '...' : '') . '」';
                    } elseif ($scout && $scout->message) {
                        $displayMessage = 'スカウト送信: 「' . mb_substr($scout->message, 0, 50) . ($scout->message > 50 ? '...' : '') . '」';
                    } else {
                        $displayMessage = 'メッセージがありません';
                    }
                @endphp
                <article class="card rounded-2xl bg-white border border-slate-200 shadow-sm p-5 md:p-7 relative overflow-hidden">
                    <div class="row flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div class="left">
                            <div class="avatar" aria-hidden="true">{{ $avatarText }}</div>
                            <div>
                                <div class="name">{{ $freelancer->display_name ?? '未設定' }}</div>
                                <div class="sub">{{ $freelancer->job_title ?? '' }}</div>
                            </div>
                        </div>
                        <div class="pill {{ $thread->is_unread ? 'unread' : '' }} md:self-start">
                            {{ $thread->is_unread ? '未読' : '未読なし' }}
                        </div>
                    </div>
                    <div class="desc">{{ $displayMessage }}</div>
                    <div class="actions flex flex-col md:flex-row justify-end gap-3 border-t border-slate-200 pt-4 mt-5">
                        <a class="btn btn-primary w-full md:w-auto" href="{{ route('company.threads.show', ['thread' => $thread->id]) }}">チャット画面へ</a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-8 md:p-10 text-center text-slate-600">
                    <p class="text-base md:text-lg font-semibold">スカウトがありません</p>
                </div>
            @endforelse
        </div>
        
        @if($threads->hasPages())
            <div class="mt-8 flex justify-center gap-2 flex-wrap">
                @if($threads->onFirstPage())
                    <span class="px-3 py-2 text-slate-500">前へ</span>
                @else
                    <a href="{{ $threads->previousPageUrl() }}" class="px-3 py-2 text-blue-600 font-bold no-underline">前へ</a>
                @endif
                
                <span class="px-3 py-2 text-slate-500">
                    {{ $threads->currentPage() }} / {{ $threads->lastPage() }}
                </span>
                
                @if($threads->hasMorePages())
                    <a href="{{ $threads->nextPageUrl() }}" class="px-3 py-2 text-blue-600 font-bold no-underline">次へ</a>
                @else
                    <span class="px-3 py-2 text-slate-500">次へ</span>
                @endif
            </div>
        @endif
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
</body>
</html>
