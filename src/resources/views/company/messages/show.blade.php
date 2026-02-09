<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募案件チャット（企業）- AITECH</title>
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

            /* UI tokens (high readability / production-grade) */
            --bg: #f6f8fb;
            --surface: #ffffff;
            --surface-2: #fbfcfe;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e6eaf2;
            --border-2: #dbe2ee;
            --primary: #0366d6;
            --primary-2: #0256cc;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 10px 30px rgba(15, 23, 42, 0.10);
            --radius-lg: 18px;
            --radius-md: 14px;
            --radius-sm: 12px;
            --focus: 0 0 0 4px rgba(3, 102, 214, 0.14);
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

        /* Page */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem;
        }
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
            padding: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
            overflow: hidden;
        }

        .chat-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: linear-gradient(180deg, var(--surface) 0%, var(--surface-2) 100%);
        }
        .chat-title {
            display: grid;
            gap: 0.2rem;
            min-width: 0;
        }
        .chat-title strong {
            font-size: 26px;
            font-weight: 900;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.02em;
        }
        .chat-title span {
            color: var(--muted);
            font-size: 26px;
            font-weight: 700;
        }

        .btn {
            padding: 0.72rem 1rem;
            border-radius: 12px;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid var(--border-2);
            background: linear-gradient(180deg, #ffffff 0%, #f7f9fc 100%);
            color: var(--text);
            font-size: 0.92rem;
            white-space: nowrap;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: var(--shadow-sm); }
        .btn:focus-visible { outline: none; box-shadow: var(--focus), var(--shadow-sm); }

        .messages {
            padding: 15px 10px 15px 25px;
            overflow-y: auto;
            display: grid;
            gap: 0.85rem;
            background:
                radial-gradient(900px 420px at 10% 0%, rgba(3, 102, 214, 0.06), transparent 60%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
            scrollbar-gutter: stable;
            flex: 1 1 auto;
        }

        .bubble-row { display: flex; align-items: flex-end; gap: 0.75rem; }
        .bubble-row.me { justify-content: flex-end; }
        .bubble-row.first-message {
            justify-content: flex-end;
            width: 100%;
            margin-left: auto;
        }
        .bubble {
            max-width: 74%;
            width: 80%;
            padding: 0.9rem 1rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.92);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            overflow-wrap: anywhere;
        }
        .bubble.me {
            background: linear-gradient(180deg, rgba(241,248,255,0.98) 0%, rgba(236,246,255,0.98) 100%);
            border-color: #cfe4ff;
            width: 80%;
            padding: 20px;
        }
        .bubble.first-message {
            max-width: 74%;
            width: 80%;
            padding: 20px;
            border-radius: 16px;
            border-color: #cfe4ff;
            background: linear-gradient(180deg, rgba(241,248,255,0.98) 0%, rgba(236,246,255,0.98) 100%);
            box-shadow: var(--shadow-sm);
            position: relative;
        }
        .bubble.first-message::before {
            display: none;
            content: "";
        }
        .bubble.first-message p {
            color: var(--text);
            font-size: 0.95rem;
            line-height: 1.7;
            white-space: pre-wrap;
        }
        .bubble.first-message small {
            color: var(--muted);
            font-weight: 800;
        }
        .bubble p { color: var(--text); font-size: 0.95rem; line-height: 1.7; white-space: pre-wrap; }
        .bubble small {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 0.45rem;
            color: var(--muted);
            font-weight: 800;
            font-size: 0.82rem;
        }

        .composer {
            padding: 1rem 1.25rem 1.25rem;
            border-top: 1px solid var(--border);
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
            background: linear-gradient(180deg, var(--surface-2) 0%, var(--surface) 100%);
        }
        .input {
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid var(--border-2);
            border-radius: 14px;
            font-size: 0.98rem;
            transition: all 0.15s ease;
            background-color: #ffffff;
            min-height: 7.25rem;
            max-height: 18rem;
            resize: vertical;
            line-height: 1.7;
            box-shadow: inset 0 1px 0 rgba(15, 23, 42, 0.03);
        }
        .input:focus {
            outline: none;
            border-color: rgba(3, 102, 214, 0.55);
            box-shadow: var(--focus);
        }
        .input.is-invalid {
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
        .send {
            padding: 14px 40px;
            border-radius: 14px;
            font-weight: 900;
            border: none;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-2) 100%);
            color: white;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 20px;
            min-width: 260px;
            max-width: 100%;
            margin-left: auto;
            box-shadow: 0 10px 20px rgba(3, 102, 214, 0.22);
        }
        .send:hover { transform: translateY(-1px); box-shadow: 0 14px 26px rgba(3, 102, 214, 0.26); }
        .send:active { transform: translateY(0px); }
        .send:focus-visible { outline: none; box-shadow: var(--focus), 0 14px 26px rgba(3, 102, 214, 0.26); }

        /* 応募ステータス用スタイル（インラインから移動） */
        .status-form {
            display: inline-flex;
            gap: 0.5rem;
            margin-right: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; scroll-behavior: auto !important; }
        }
        /* 応募ステータス用スタイル（インラインから移動） */
        .status-label {
            font-weight: 900;
            color: #586069;
            font-size: 1.3em; /* 1.3倍 */
            display: inline-block;
            margin-right: 0.4rem;
        }
        .status-select {
            font-size: 1.3em; /* 1.3倍 */
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    @include('partials.company-header')

    <main class="main-content max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-10">
        <section class="panel chat-pane" aria-label="応募チャット">
            <div class="chat-header">
                <div class="chat-title">
                    <strong>{{ $thread->job ? $thread->job->title : '案件' }}</strong>
                    <span>{{ $thread->freelancer->display_name ?? '不明' }}とのチャット</span>
                </div>
                <div class="chat-tools flex flex-col md:flex-row md:items-center gap-3 md:gap-4">
                    @if($application)
                        <form method="POST" action="{{ route('company.threads.application-status.update', ['thread' => $thread]) }}" id="statusForm" class="status-form">
                            @csrf
                            @method('PATCH')
                            <label for="statusSelect" class="status-label">応募ステータス</label>
                            <select class="select status-select" id="statusSelect" name="status" aria-label="応募ステータス">
                                <option value="0" {{ $application->status === \App\Models\Application::STATUS_PENDING ? 'selected' : '' }}>未対応</option>
                                <option value="1" {{ $application->status === \App\Models\Application::STATUS_IN_PROGRESS ? 'selected' : '' }}>対応中</option>
                                <option value="2" {{ $application->status === \App\Models\Application::STATUS_CLOSED ? 'selected' : '' }}>クローズ（終了）</option>
                            </select>
                        </form>
                    @endif
                    <a class="btn" href="{{ route('company.applications.index') }}">一覧へ</a>
                </div>
            </div>

            <div class="messages max-h-[70vh] md:max-h-[64vh] lg:max-h-[66vh]" id="messages" aria-label="メッセージ一覧">
                @php
                    $activeMessages = $messages->whereNull('deleted_at')->sortBy('sent_at')->values();
                    $latestMessage = $activeMessages->last();
                    $freelancerInitial = mb_substr($thread->freelancer->display_name ?? 'F', 0, 1);
                @endphp

                @forelse($activeMessages as $message)
                    @php
                        $isMe = $message->sender_type === 'company';
                        $isFirst = $loop->first;
                        $sentAt = $message->sent_at ? $message->sent_at->format('Y/m/d H:i') : '';
                        $isLatest = $latestMessage && $latestMessage->id === $message->id;
                        $canDelete = $isMe && $isLatest;
                    @endphp

                    @if($isFirst)
                        <div class="bubble-row first-message {{ $isMe ? 'me' : '' }}">
                            <div class="bubble first-message">
                                <p>{{ $message->body }}</p>
                                <small>
                                    {{ $sentAt }}
                                    @if($canDelete)
                                        <span style="margin-left:0.75rem;">
                                            <form action="{{ route('company.messages.destroy', ['message' => $message]) }}" method="POST" style="display:inline;" class="delete-form" data-message-id="{{ $message->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="delete-trigger" style="background:none;border:none;color:#d73a49;font-weight:900;cursor:pointer;">削除</button>
                                            </form>
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="bubble-row {{ $isMe ? 'me' : '' }}">
                            <div class="bubble {{ $isMe ? 'me' : '' }}">
                                <p>{{ $message->body }}</p>
                                <small>
                                    {{ $sentAt }}
                                    @if($canDelete)
                                        <span style="margin-left:0.75rem;">
                                            <form action="{{ route('company.messages.destroy', ['message' => $message]) }}" method="POST" style="display:inline;" class="delete-form" data-message-id="{{ $message->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="delete-trigger" style="background:none;border:none;color:#d73a49;font-weight:900;cursor:pointer;">削除</button>
                                            </form>
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endif
                @empty
                    <div style="text-align: center; padding: 2rem; color: #6a737d;">
                        <p>メッセージがありません。</p>
                    </div>
                @endforelse
            </div>

            <form class="composer" method="POST" action="{{ route('company.threads.messages.store', ['thread' => $thread]) }}" id="messageForm">
                @csrf
                <textarea class="input @error('content') is-invalid @enderror" id="messageInput" name="content" placeholder="メッセージを入力（クローズ時は送信不可）" aria-label="メッセージ入力"></textarea>
                @error('content')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <button class="send w-full md:w-auto" id="sendBtn" type="submit">送信</button>
            </form>
        </section>
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
            const status = document.getElementById('statusSelect');
            const statusForm = document.getElementById('statusForm');
            const input = document.getElementById('messageInput');
            const btn = document.getElementById('sendBtn');
            if (!status || !input || !btn) return;

            const apply = () => {
                const closed = status.value === '2';
                input.disabled = closed;
                btn.disabled = closed;
                btn.style.opacity = closed ? '0.5' : '1';
                btn.style.pointerEvents = closed ? 'none' : 'auto';
            };
            
            // ステータス変更時に自動送信
            if (statusForm) {
                status.addEventListener('change', () => {
                    statusForm.submit();
                });
            }
            
            apply();
        })();
    </script>
    <script>
        (function () {
            const el = document.getElementById('messages');
            if (!el) return;
            el.scrollTop = el.scrollHeight;
        })();
    </script>
    <!-- 削除確認モーダル -->
    <div id="confirmDeleteModal" role="dialog" aria-hidden="true" aria-labelledby="confirmDeleteTitle" style="display:block;">
        <div style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;z-index:1000;">
            <div id="confirmDeleteDialog" style="pointer-events:auto;width:min(540px,92%);background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.15);padding:1.25rem;display:none;" aria-modal="true">
                <h2 id="confirmDeleteTitle" style="margin:0 0 0.5rem;font-size:1.05rem;font-weight:800;color:#0f172a;">本当に削除しますか？</h2>
                <p style="margin:0 0 1rem;color:#64748b;">この操作は取り消せません。よろしければ「削除する」をクリックしてください。</p>
                <div style="display:flex;gap:0.75rem;">
                    <button id="cancelDeleteBtn" style="flex:1;padding:0.6rem 0.9rem;border-radius:8px;border:1px solid #e6eaf2;background:#fafbfc;cursor:pointer;">キャンセル</button>
                    <button id="confirmDeleteBtn" style="flex:1;padding:0.6rem 0.9rem;border-radius:8px;border:none;background:#d73a49;color:#fff;cursor:pointer;">削除する</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            let pendingForm = null;
            const modal = document.getElementById('confirmDeleteModal');
            const dialog = document.getElementById('confirmDeleteDialog');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            const cancelBtn = document.getElementById('cancelDeleteBtn');
            function openModal(form) {
                pendingForm = form;
                if (modal && dialog) {
                    modal.setAttribute('aria-hidden','false');
                    dialog.style.display = 'block';
                    modal.classList.add('is-open');
                    confirmBtn && confirmBtn.focus();
                }
            }
            function closeModal() {
                pendingForm = null;
                if (modal && dialog) {
                    dialog.style.display = 'none';
                    modal.setAttribute('aria-hidden','true');
                    modal.classList.remove('is-open');
                }
            }
            document.addEventListener('click', (e) => {
                const trigger = e.target.closest && e.target.closest('.delete-trigger');
                if (trigger) { e.preventDefault(); const form = trigger.closest('form'); if (form) openModal(form); }
            });
            modal && modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            cancelBtn && cancelBtn.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
            confirmBtn && confirmBtn.addEventListener('click', (e) => { e.preventDefault(); if (!pendingForm) return closeModal(); pendingForm.submit(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) closeModal(); });
        })();
    </script>
</body>
</html>