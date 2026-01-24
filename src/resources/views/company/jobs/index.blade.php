<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自社案件一覧（企業）- AITECH</title>
    <style>
        :root { --header-height: 104px; --header-height-mobile: 91px; }
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
        .sidebar {
            width: 320px;
            flex-shrink: 0;
            position: sticky;
            top: calc(var(--header-height) + 1.5rem);
            align-self: flex-start;
        }
        .content-area { flex: 1; min-width: 0; }
        .panel {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
            margin-bottom: 2rem;
        }
        .panel h3 { font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; color: #24292e; letter-spacing: -0.01em; }
        .field { margin-bottom: 1.25rem; }
        .field label { display: block; font-weight: 800; margin-bottom: 0.75rem; color: #586069; font-size: 0.9rem; }
        .input, .select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .input:focus, .select:focus { outline: none; border-color: #0366d6; box-shadow: 0 0 0 3px rgba(3,102,214,0.1); background-color: #fff; }
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
            line-height: 1;               /* 明示的に行間を統一 */
            vertical-align: middle;       /* inline 要素の揃えを統一 */
            min-height: 48px;             /* ボタンの最小縦サイズを揃える */
            box-sizing: border-box;
        }
        .btn-primary { background-color: #0366d6; color: #fff; font-size: 20px; padding: 15px 60px; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3,102,214,0.3); }
        .btn-secondary { background-color: #586069; color: #fff; font-size: 20px; padding: 15px 60px; max-height: 50px; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }
        .btn-danger { background-color: #d73a49; color: #fff; font-size: 20px; padding: 15px 60px; max-height: 60px; }
        .btn-danger:hover { background-color: #c62f3c; transform: translateY(-1px); }

        .page-title {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            color: #24292e;
            letter-spacing: -0.025em;
        }
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

        /* Job header & title (match freelancer view) */
        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
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
        .desc { color: #586069; margin-top: 0.75rem; line-height: 1.65; }

        /* Skills tags (match freelancer view) */
        .skills-section {
            margin-top: 1rem;
            margin-bottom: 1.5rem;
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
        .job-details {
            display: grid;
            max-width: 600px;
            grid-template-columns: repeat(auto-fit, minmax(90px, calc(50% - 25px)));
            margin-bottom: 2rem;
            margin-top: 1rem;
        }
        .detail-item {
            display: flex;
            max-width: 250px;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
            background-color: #f6f8fa;
            border-radius: 8px;
        }
        .detail-label {
            font-size: 1rem;
            color: #6a737d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
            white-space: nowrap;
        }
        .detail-value {
            font-weight: 700;
            color: #24292e;
            font-size: 1.1rem;
            white-space: nowrap;
        }
        .skills-section {
            margin-top: 1rem;
            padding: 0.85rem;
            background-color: #f6f8fa;
            border-radius: 10px;
        }
        .skills-label { font-size: 0.75rem; color: #6a737d; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
        .skills-value { font-weight: 900; color: #24292e; }
        .pill {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-weight: 900;
            border: 1px solid #e1e4e8;
            background: #fafbfc;
            white-space: nowrap;
            font-size: 0.85rem;
        }
        .pill.public { background: #e6ffed; border-color: #b7f5c3; color: #1a7f37; }
        .pill.draft { background: #fff8c5; border-color: #f5e58a; color: #7a5d00; }
        .pill.stopped { background: #fff5f5; border-color: #ffccd2; color: #b31d28; }
        .actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e1e4e8; flex-wrap: wrap; }
        .inline { display: inline-flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        .pagination-list {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
            justify-content: center;
        }
        .pagination-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            text-decoration: none;
            color: #586069;
            font-weight: 600;
            font-size: 0.875rem;
            border: 1px solid #e1e4e8;
            background-color: white;
            transition: all 0.15s ease;
            min-width: 36px;
        }
        .pagination-link:hover {
            background-color: #f6f8fa;
            border-color: #d1d5da;
            color: #24292e;
        }
        .pagination-link.is-active {
            background-color: #0366d6;
            color: white;
            border-color: #0366d6;
        }
        .pagination-link.is-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        .pagination-ellipsis {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem;
            color: #586069;
        }

        /* Delete Confirmation Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .modal-overlay.is-open {
            display: flex;
            opacity: 1;
        }
        .modal-dialog {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 8px 24px rgba(0, 0, 0, 0.2);
            max-width: 480px;
            width: 90%;
            max-height: 90vh;
            overflow: auto;
            transform: scale(0.95) translateY(20px);
            transition: transform 0.2s ease;
            border: 1px solid #e1e4e8;
        }
        .modal-overlay.is-open .modal-dialog {
            transform: scale(1) translateY(0);
        }
        .modal-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid #e1e4e8;
        }
        .modal-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #24292e;
            margin: 0;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .modal-title-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .modal-body {
            padding: 1.5rem 2rem;
        }
        .modal-message {
            color: #586069;
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
        }
        .modal-footer {
            padding: 1rem 2rem 2rem;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
        .modal-footer .btn {
            min-width: 100px;
        }

        /* Flash messages (success / error) */
        .flash-success {
            background-color: #e6ffed;
            border: 1px solid #b7f5c3;
            color: #1a7f37;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: bold;
            font-size: 18px;
        }
        .flash-error {
            background-color: #fff5f5;
            border: 1px solid #ffccd2;
            color: #b31d28;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: bold;
            font-size: 18px;
        }

        @media (max-width: 920px) {
            .header-content { height: var(--header-height-mobile); }
            .nav-links { position: static; left: auto; transform: none; justify-content: flex-start; }
            .user-menu { position: static; transform: none; margin-left: auto; }
            .main-content { flex-direction: column; padding: 1.5rem; }
            .sidebar { width: 100%; position: static; top: auto; order: -1; }
            .job-details { grid-template-columns: 1fr; }
            .actions .btn { width: 100%; }
        }
        @media (max-width: 1200px) {
            .nav-links { gap: 1.5rem; }
            .nav-link { font-size: 1rem; padding: 0.5rem 1rem; }
            .nav-link.has-badge { padding-right: 3rem; }
        }
    </style>
    @include('partials.aitech-responsive')
</head>
<body>
    <header class="header">
        <div class="header-content">
            <nav class="nav-links">
                <a href="{{ route('company.freelancers.index') }}" class="nav-link">フリーランス一覧</a>
                <a href="{{ route('company.jobs.index') }}" class="nav-link active">案件一覧</a>
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
                <a href="{{ route('company.scouts.index') }}" class="nav-link {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                    スカウト
                    @if($scoutUnread > 0)
                        <span class="badge">{{ $scoutUnread }}</span>
                    @endif
                </a>
            </nav>
            <div class="user-menu">
                <div class="dropdown" id="userDropdown">
                    <button class="user-avatar" id="userDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="userDropdownMenu">企</button>
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
        <aside class="sidebar">
            <div class="panel">
                <h3>検索</h3>
                <form method="GET" action="{{ route('company.jobs.index') }}">
                    <div class="field">
                        <label for="keyword">キーワード</label>
                        <input id="keyword" name="keyword" class="input" type="text" placeholder="タイトル / 概要 / スキル など" value="{{ old('keyword', $keyword ?? '') }}">
                    </div>
                    <button class="btn btn-primary" type="submit">検索</button>
                    @if(isset($keyword) && $keyword !== '')
                        <a href="{{ route('company.jobs.index') }}" class="btn btn-secondary" style="margin-top: 0.5rem; display: block; text-align: center;">リセット</a>
                    @endif
                </form>
            </div>
        </aside>

        <section class="content-area">
            <div class="topbar">
                <h1 class="page-title">自社案件一覧</h1>
                <a class="btn btn-primary" href="{{ route('company.jobs.create') }}">新規登録</a>
            </div>

            @if(session('success'))
                <div class="flash-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flash-error">
                    {{ session('error') }}
                </div>
            @endif

            <div class="list">
                @forelse($jobs as $job)
                    @php
                        $statusClass = '';
                        $statusText = '';
                        $statusValue = '';
                        switch($job->status) {
                            case \App\Models\Job::STATUS_PUBLISHED:
                                $statusClass = 'public';
                                $statusText = '公開';
                                $statusValue = 'publish';
                                break;
                            case \App\Models\Job::STATUS_DRAFT:
                                $statusClass = 'draft';
                                $statusText = '下書き';
                                $statusValue = 'draft';
                                break;
                            case \App\Models\Job::STATUS_STOPPED:
                                $statusClass = 'stopped';
                                $statusText = '停止';
                                $statusValue = 'stopped';
                                break;
                        }
                        
                        // 報酬の表示フォーマット
                        $rewardDisplay = '';
                        if ($job->reward_type === 'monthly') {
                            $rewardDisplay = ($job->min_rate / 10000) . '〜' . ($job->max_rate / 10000) . '万円';
                        } else {
                            $rewardDisplay = number_format($job->min_rate) . '〜' . number_format($job->max_rate) . '円/時';
                        }
                    @endphp
                    <article class="card">
                        <div class="job-header">
                            <div>
                                <h2 class="job-title">{{ $job->title }}</h2>
                                <div class="company-name">{{ $company->name }}</div>
                            </div>
                            <div class="inline">
                                <span class="pill {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                        </div>

                        <div class="desc">{{ $job->description }}</div>

                        <div class="job-details">
                            <div class="detail-item">
                                <div class="detail-label">稼働時間</div>
                                <div class="detail-value">{{ $job->work_time_text }}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">報酬</div>
                                <div class="detail-value">{{ $rewardDisplay }}</div>
                            </div>
                        </div>

                        @if($job->required_skills_text)
                            @php $skills = explode(',', $job->required_skills_text); @endphp
                            <div class="skills-section">
                                <div class="skills">
                                    @foreach($skills as $skill)
                                        <span class="skill-tag">{{ trim($skill) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="actions">
                            <form method="POST" action="{{ route('company.jobs.status.update', $job) }}" style="margin-right:auto;">
                                @csrf
                                @method('PATCH')
                                <label class="inline" style="font-weight:900; color:#586069;">
                                    ステータス
                                    <select name="status" class="select" aria-label="ステータス" onchange="this.form.submit()">
                                        <option value="publish" {{ $job->status === \App\Models\Job::STATUS_PUBLISHED ? 'selected' : '' }}>公開</option>
                                        <option value="draft" {{ $job->status === \App\Models\Job::STATUS_DRAFT ? 'selected' : '' }}>下書き</option>
                                        <option value="stopped" {{ $job->status === \App\Models\Job::STATUS_STOPPED ? 'selected' : '' }}>停止</option>
                                    </select>
                                </label>
                            </form>
                            <a class="btn btn-secondary" href="{{ route('company.jobs.edit', $job) }}">編集</a>
                            <form method="POST" action="{{ route('company.jobs.destroy', $job) }}" style="display: inline;" id="delete-form-{{ $job->id }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger delete-btn" type="button" data-job-id="{{ $job->id }}" data-job-title="{{ $job->title }}">削除</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div style="text-align: center; padding: 3rem; color: #586069;">
                        <p style="font-size: 1.1rem; margin-bottom: 1rem;">案件がありません</p>
                    </div>
                @endforelse
            </div>

            @if($jobs->hasPages())
                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $jobs->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2 class="modal-title">
                    <span class="modal-title-icon">⚠</span>
                    削除の確認
                </h2>
            </div>
            <div class="modal-body">
                <p class="modal-message" id="deleteModalMessage">本当にこの案件を削除しますか？</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">キャンセル</button>
                <button type="button" class="btn btn-danger" id="deleteConfirmBtn">削除する</button>
            </div>
        </div>
    </div>

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

        // Delete Modal Functions
        let currentDeleteFormId = null;

        function openDeleteModal(jobId, jobTitle) {
            const modal = document.getElementById('deleteModal');
            const message = document.getElementById('deleteModalMessage');
            const confirmBtn = document.getElementById('deleteConfirmBtn');
            
            currentDeleteFormId = jobId;
            message.textContent = '「' + jobTitle + '」を本当に削除しますか？この操作は取り消せません。';
            
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            
            // Focus on cancel button for accessibility
            setTimeout(function() {
                confirmBtn.focus();
            }, 100);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
            currentDeleteFormId = null;
        }

        function confirmDelete() {
            if (currentDeleteFormId) {
                const form = document.getElementById('delete-form-' + currentDeleteFormId);
                if (form) {
                    form.submit();
                }
            }
        }

        // Modal event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('deleteModal');
            const confirmBtn = document.getElementById('deleteConfirmBtn');
            const deleteButtons = document.querySelectorAll('.delete-btn');
            
            // Attach click handlers to all delete buttons
            deleteButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const jobId = this.getAttribute('data-job-id');
                    const jobTitle = this.getAttribute('data-job-title');
                    openDeleteModal(jobId, jobTitle);
                });
            });
            
            // Close modal when clicking overlay
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeDeleteModal();
                }
            });
            
            // Confirm button
            confirmBtn.addEventListener('click', confirmDelete);
            
            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeDeleteModal();
                }
            });
        });
    </script>
</body>
</html>
