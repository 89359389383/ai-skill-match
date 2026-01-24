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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 97.5%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        /* Header Styles - Minimalist */
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: var(--header-height);
            position: relative;
        }
        .nav-links {
            display: flex;
            flex-direction: row;
            gap: 3rem;
            align-items: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            justify-content: center;
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
        }
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
        .user-menu {
            display: flex;
            align-items: center;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
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

        /* Responsive */
        @media (max-width: 1200px) {
            .main-content { padding: 2rem; }
        }
        @media (max-width: 768px) {
            .header-content { padding: 0 1.5rem; height: var(--header-height-mobile); }
            .nav-links { gap: 1.5rem; position: static; left: auto; transform: none; justify-content: flex-start; flex-direction: row; flex-wrap: wrap; }
            .user-menu { position: static; right: auto; top: auto; transform: none; margin-left: auto; }
            .nav-link { padding: 0.5rem 1rem; font-size: 1rem; }
            .main-content { padding: 1.5rem; }
            .actions { flex-direction: column; }
            .btn { width: 100%; }
            .kv { grid-template-columns: 1fr; }
        }
    </style>
    @include('partials.aitech-responsive')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <nav class="nav-links">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link">案件一覧</a>
                @php
                    $totalUnreadCount = ($unreadApplicationCount ?? 0) + ($unreadScoutCount ?? 0);
                @endphp
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ $totalUnreadCount > 0 ? 'has-badge' : '' }} active">
                    応募した案件
                    @if($totalUnreadCount > 0)
                        <span class="badge">{{ $totalUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ $totalUnreadCount > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($totalUnreadCount > 0)
                        <span class="badge">{{ $totalUnreadCount }}</span>
                    @endif
                </a>
            </nav>
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
    </header>

    <main class="main-content">
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

                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('freelancer.jobs.show', $job) }}" role="button">戻る</a>
                        <button class="btn btn-primary" type="submit">応募を送信</button>
                    </div>
                </form>
            </div>

        </div>
    </main>

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