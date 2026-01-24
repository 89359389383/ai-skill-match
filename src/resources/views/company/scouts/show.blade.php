<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スカウトチャット（企業）- AITECH</title>
    <style>
        :root {
            --header-height: 104px;       /* 80px * 1.3 */
            --header-height-mobile: 91px; /* 70px * 1.3 */

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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 97.5%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        /* Header */
        .header {
            background-color: #ffffff;
            border-bottom: 1px solid #e1e4e8;
            padding: 0 3rem;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .header-content {
            max-width: 1600px;
            margin: 0 auto;
            border-bottom: 1px solid #e1e4e8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: var(--header-height);
            position: relative;
        }
        .nav-links {
            display: flex;
            gap: 3rem;
            align-items: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            justify-content: center;
            flex-wrap: nowrap;
        }
        .nav-link {
            text-decoration: none;
            color: #586069;
            font-weight: 500;
            font-size: 1.1rem;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            transition: all 0.15s ease;
            position: relative;
            letter-spacing: -0.01em;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        .nav-link.has-badge { padding-right: 3rem; }
        .nav-link:hover { background-color: #f6f8fa; color: #24292e; }
        .nav-link.active {
            background-color: #0366d6;
            color: white;
            box-shadow: 0 2px 8px rgba(3, 102, 214, 0.3);
        }
        .badge {
            background-color: #d73a49; color: white; border-radius: 50%;
            padding: 0.15rem 0.45rem; font-size: 0.7rem; font-weight: 600;
            min-width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center;
            box-shadow: 0 1px 3px rgba(209, 58, 73, 0.3);
            position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        }
        .user-menu { display: flex; align-items: center; position: absolute; right: 0; top: 50%; transform: translateY(-50%); }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 600; cursor: pointer; transition: all 0.15s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: none; padding: 0; appearance: none;
        }
        .user-avatar:hover { transform: scale(1.08); box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
        .dropdown { position: relative; }
        .dropdown-content {
            display: none; position: absolute; right: 0; top: 100%;
            background-color: white; min-width: 240px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 12px; z-index: 1000; border: 1px solid #e1e4e8; margin-top: 0.5rem;
        }
        .dropdown.is-open .dropdown-content { display: block; }
        .dropdown-item {
            display: block; padding: 0.875rem 1.25rem; text-decoration: none; color: #586069;
            transition: all 0.15s ease; border-radius: 6px; margin: 0 0.25rem; white-space: nowrap;
        }
        .dropdown-item:hover { background-color: #f6f8fa; color: #24292e; }
        .dropdown-divider { height: 1px; background-color: #e1e4e8; margin: 0.5rem 0; }

        /* Page */
        .main-content { max-width: 1200px; margin: 0 auto; padding: 3rem; }
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
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.02em;
        }
        .chat-title span {
            color: var(--muted);
            font-size: 28px;
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

        /* Responsive */
        @media (max-width: 1200px) {
            .main-content { padding: 2rem 1.25rem 2.5rem; }
        }
        @media (max-width: 900px) {
            .messages { max-height: 500px; }
        }
        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; scroll-behavior: auto !important; }
        }
        /* Delete confirmation modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2,6,23,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .modal-overlay.is-open { display: flex; }
        .modal-dialog {
            background: var(--surface);
            border-radius: 12px;
            padding: 1.5rem;
            max-width: 420px;
            width: calc(100% - 2rem);
            box-shadow: 0 14px 40px rgba(2,6,23,0.18);
            border: 1px solid var(--border-2);
            text-align: left;
        }
        .modal-dialog h2 {
            margin: 0 0 0.5rem 0;
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--text);
        }
        .modal-dialog p { color: var(--muted); margin: 0 0 1rem 0; }
        .modal-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }
        .modal-actions > button {
            flex: 1;
            min-width: 0; /* allow shrinking in flex */
            font-size: 0.92rem; /* match .btn */
            padding: 0.72rem 1rem;
            font-weight: 800;
            border-radius: 10px;
        }
        .btn-danger {
            background: linear-gradient(180deg, #d73a49 0%, #c5303f 100%);
            border: none;
            color: white;
            cursor: pointer;
        }
        .modal-actions .btn {
            background: linear-gradient(180deg, #ffffff 0%, #f7f9fc 100%);
            color: var(--text);
            border: 1px solid var(--border-2);
        }
    </style>
    @include('partials.aitech-responsive')
</head>
<body>
    <header class="header">
        <div class="header-content">
            <nav class="nav-links">
                <a href="{{ route('company.freelancers.index') }}" class="nav-link">フリーランス一覧</a>
                <a href="{{ route('company.jobs.index') }}" class="nav-link">案件一覧</a>
                @php
                    $appUnread = ($unreadApplicationCount ?? 0);
                    $scoutUnread = ($unreadScoutCount ?? 0);
                @endphp
                <a href="{{ route('company.applications.index') }}" class="nav-link {{ $appUnread > 0 ? 'has-badge' : '' }}">
                    応募された案件
                    @if($appUnread > 0)
                        <span class="badge">{{ $appUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('company.scouts.index') }}" class="nav-link {{ $scoutUnread > 0 ? 'has-badge' : '' }} active">
                    スカウト
                    @if($scoutUnread > 0)
                        <span class="badge">{{ $scoutUnread }}</span>
                    @endif
                </a>
            </nav>
            <div class="user-menu">
                <div class="dropdown" id="userDropdown">
                    <button class="user-avatar" id="userDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="userDropdownMenu">{{ $userInitial ?? '企' }}</button>
                    <div class="dropdown-content" id="userDropdownMenu" role="menu" aria-label="ユーザーメニュー">
                        <a href="{{ route('company.profile.settings') }}" class="dropdown-item" role="menuitem">プロフィール設定</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('auth.logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item" role="menuitem" style="width: 100%; text-align: left; background: none; border: none; padding: 0.875rem 1.25rem; color: #586069; cursor: pointer; font-size: inherit; font-family: inherit;">ログアウト</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <section class="panel chat-pane" aria-label="スカウトチャット">
            <div class="chat-header">
                <div class="chat-title">
                    <span>{{ $thread->freelancer->display_name ?? '不明' }}とのチャット</span>
                </div>
                <a class="btn" href="{{ route('company.scouts.index') }}">一覧へ</a>
            </div>

            <div class="messages" id="messages" aria-label="メッセージ一覧">
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
                                            <form action="{{ route('company.messages.destroy', ['message' => $message->id]) }}" method="POST" style="display:inline;" class="delete-form" data-message-id="{{ $message->id }}">
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
                                            <form action="{{ route('company.messages.destroy', ['message' => $message->id]) }}" method="POST" style="display:inline;" class="delete-form" data-message-id="{{ $message->id }}">
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

            <form class="composer" action="{{ route('company.threads.messages.store', ['thread' => $thread->id]) }}" method="POST">
                @csrf
                <textarea class="input @error('content') is-invalid @enderror" name="content" placeholder="メッセージを入力…" aria-label="メッセージ入力"></textarea>
                @error('content')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <button class="send" type="submit">送信</button>
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
