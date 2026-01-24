<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募した案件 - AITECH</title>
    {{-- ヘッダーに必要なスタイルのみをここに記載 --}}
    <style>
        :root {
            --header-height: 104px;
            --header-height-mobile: 91px;
        }
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
        @media (max-width: 768px) {
            .header-content { padding: 0 1.5rem; height: var(--header-height-mobile); }
            .nav-links { gap: 1.5rem; position: static; left: auto; transform: none; justify-content: flex-start; flex-direction: row; flex-wrap: wrap; }
            .user-menu { position: static; right: auto; top: auto; transform: none; margin-left: auto; }
            .nav-link { padding: 0.5rem 1rem; font-size: 1rem; }
        }
    </style>
    <style>
        :root {
            --header-height: 104px;       /* 80px * 1.3 */
            --header-height-mobile: 91px; /* 70px * 1.3 */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: 97.5%;
        }

        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        /* Main Layout - Clean and Spacious */
        .main-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 3rem;
        }

        /* Content Area */
        .content-area {
            width: 100%;
        }

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
            display: block;
            text-decoration: none;
            color: inherit;
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
            margin-bottom: 1.25rem;
            gap: 1rem;
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

        .job-meta {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.85rem;
            border: 1px solid #e1e4e8;
            background: #fafbfc;
            color: #24292e;
            white-space: nowrap;
        }

        .chip.status-pending {
            background: #fff5b1;
            border-color: #f1e05a;
            color: #7a5a00;
        }
        .chip.status-interview {
            background: #f1f8ff;
            border-color: #c8e1ff;
            color: #0366d6;
        }
        .chip.status-closed {
            background: #f6f8fa;
            border-color: #d1d5da;
            color: #6a737d;
        }

        .chip.unread {
            background: #ffeef0;
            border-color: #ffdce0;
            color: #d73a49;
        }

        /* Tabs Bar - Under Header */
        .tabs-bar {
            background-color: #ffffff;
            border-bottom: 1px solid #e1e4e8;
            padding: 0 3rem;
            position: sticky;
            top: var(--header-height);
            z-index: 99;
        }

        .tabs-container {
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            gap: 0;
        }

        .tab-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #586069;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            border-bottom: 3px solid transparent;
            transition: all 0.15s ease;
            position: relative;
            letter-spacing: -0.01em;
        }

        .tab-link:hover {
            color: #24292e;
            background-color: #f6f8fa;
        }

        .tab-link.active {
            color: #0366d6;
            border-bottom-color: #0366d6;
            background-color: transparent;
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

        .skills-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #586069;
            margin-bottom: 0.75rem;
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

        .btn-secondary:hover {
            background-color: #4c5561;
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: #0366d6;
            color: white;
            font-size: 20px;
            padding: 15px 60px;
        }

        .btn-primary:hover {
            background-color: #0256cc;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3);
        }

        .btn-danger {
            background-color: #d73a49;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c7303f;
            transform: translateY(-1px);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                padding: 2rem;
            }

            .tabs-bar {
                padding: 0 2rem;
            }

            .job-details {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                padding: 0 1.5rem;
                height: var(--header-height-mobile);
            }

            .nav-links {
                gap: 1.5rem;
                position: static;
                left: auto;
                transform: none;
                justify-content: flex-start;
                flex-direction: row;
                flex-wrap: wrap;
            }

            .user-menu {
                position: static;
                right: auto;
                top: auto;
                transform: none;
                margin-left: auto;
            }

            .nav-link {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }

            .tabs-bar {
                padding: 0 1.5rem;
                top: var(--header-height-mobile);
            }

            .tab-link {
                padding: 0.875rem 1rem;
                font-size: 0.95rem;
            }

            .main-content {
                padding: 1.5rem;
            }

            .job-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .job-details {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .tabs-bar { padding: 0 1rem; }
            .tab-link { padding: 0.8rem 0.9rem; font-size: 0.95rem; }
            .main-content { padding: 1.25rem; }
            .job-card { padding: 1.25rem; }
            .job-title { font-size: 1.2rem; }
        }
    </style>
    @include('partials.aitech-responsive')
</head>
<body>
    <header class="header" role="banner">
        <div class="header-content">
            <nav class="nav-links" role="navigation" aria-label="フリーランスナビゲーション">
                <a href="{{ route('freelancer.jobs.index') }}" class="nav-link">案件一覧</a>
                @php
                    $appUnread = ($unreadApplicationCount ?? 0);
                    $scoutUnread = ($unreadScoutCount ?? 0);
                @endphp
                <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ Request::routeIs('freelancer.applications.*') ? 'active' : '' }} {{ $appUnread > 0 ? 'has-badge' : '' }}">
                    応募した案件
                    @if($appUnread > 0)
                        <span class="badge" aria-live="polite">{{ $appUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ Request::routeIs('freelancer.scouts.*') ? 'active' : '' }} {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($scoutUnread > 0)
                        <span class="badge" aria-hidden="false">{{ $scoutUnread }}</span>
                    @endif
                </a>
            </nav>

            <div class="user-menu" role="region" aria-label="ユーザー">
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
    </header>
    <!-- Tabs Bar -->
    <div class="tabs-bar">
        <div class="tabs-container">
            <nav aria-label="応募一覧タブ">
                <a class="tab-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('freelancer.applications.index', ['status' => 'pending']) }}" data-tab="active" id="tab-active">応募中</a>
                <a class="tab-link {{ $status === 'closed' ? 'active' : '' }}" href="{{ route('freelancer.applications.index', ['status' => 'closed']) }}" data-tab="closed" id="tab-closed">終了</a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Content Area -->
        <div class="content-area">
            <div class="jobs-grid" id="jobs-grid">
                @forelse($applications as $application)
                    @php
                        $job = $application->job;
                        $company = $job->company ?? null;
                        $thread = $application->thread ?? null;
                        $isUnread = $application->is_unread ?? false;
                        
                        // ステータス表示用
                        $statusLabels = [
                            \App\Models\Application::STATUS_PENDING => '未対応',
                            \App\Models\Application::STATUS_IN_PROGRESS => '対応中',
                            \App\Models\Application::STATUS_CLOSED => 'クローズ',
                        ];
                        $statusLabel = $statusLabels[$application->status] ?? '不明';
                        
                        // ステータス用のCSSクラス名
                        $statusClassMap = [
                            \App\Models\Application::STATUS_PENDING => 'status-pending',
                            \App\Models\Application::STATUS_IN_PROGRESS => 'status-interview',
                            \App\Models\Application::STATUS_CLOSED => 'status-closed',
                        ];
                        $statusClass = $statusClassMap[$application->status] ?? '';
                        
                        // 報酬表示用
                        $rewardText = '';
                        if ($job->reward_type === 'monthly') {
                            $rewardText = ($job->min_rate / 10000) . '〜' . ($job->max_rate / 10000) . '万円';
                        } elseif ($job->reward_type === 'hourly') {
                            $rewardText = number_format($job->min_rate) . '〜' . number_format($job->max_rate) . '円/時';
                        }
                        
                        // スキル表示用（カンマ区切りを配列に変換）
                        $skills = [];
                        if (!empty($job->required_skills_text)) {
                            $skills = array_map('trim', explode(',', $job->required_skills_text));
                            $skills = array_filter($skills);
                        }
                        
                        // クローズ済みかどうか
                        $isClosed = $application->status === \App\Models\Application::STATUS_CLOSED;
                    @endphp
                    <div class="job-card {{ $isClosed ? 'closed-job' : '' }}" style="{{ $isClosed && $status === 'pending' ? 'display:none;' : '' }}" role="button" tabindex="0">
                        <div class="job-header">
                            <div>
                                <h2 class="job-title">{{ $job->title }}</h2>
                                <div class="company-name">{{ $company->name ?? '企業名不明' }}</div>
                            </div>
                            <div class="job-meta">
                                @if($isUnread)
                                    <span class="chip unread">未読</span>
                                @endif
                                <span class="chip {{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>
                        </div>
                        <p class="job-description">{{ $application->message }}</p>
                        <div class="job-details">
                            <div class="detail-item">
                                <div class="detail-label">想定稼働時間／期間</div>
                                <div class="detail-value">{{ $job->work_time_text }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">報酬</div>
                                <div class="detail-value">{{ $rewardText }}</div>
                            </div>
                        </div>
                        @if(count($skills) > 0)
                            <div class="skills-section">
                                <div class="skills-title">必要スキル</div>
                                <div class="skills">
                                    @foreach($skills as $skill)
                                        <span class="skill-tag">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="job-actions">
                            <a href="{{ route('freelancer.jobs.show', ['job' => $job->id]) }}" class="btn btn-secondary">案件詳細</a>
                            @if($thread)
                                <a href="{{ route('freelancer.threads.show', ['thread' => $thread->id]) }}" class="btn btn-primary">チャットを開く</a>
                            @else
                                <span class="btn btn-primary" style="opacity:0.6;cursor:not-allowed;">チャット（準備中）</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem; color: #6a737d;">
                        <p style="font-size: 1.1rem;">応募した案件がありません。</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>

    <script>
        (function () {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.getElementById('userDropdownToggle');
            const menu = document.getElementById('userDropdownMenu');
            if (dropdown && toggle && menu) {
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
            }

            // Tab switching is now handled by server-side routing
        })();
    </script>
</body>
</html>
