<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>応募された案件（企業）- AITECH</title>
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

        /* Main */
        .main-content { max-width: 1000px; margin: 0 auto; padding: 2.25rem 3rem 3rem; background-color: #fafbfc; }
        .page-head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }
        .page-title {
            font-size: 2.6rem;
            font-weight: 900;
            color: #24292e;
            letter-spacing: -0.025em;
        }
        .total-unread {
            background: #d73a49;
            color: #fff;
            border-radius: 999px;
            padding: 0.6rem 1.15rem;
            font-size: 1.17rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            line-height: 1;
            box-shadow: 0 2px 10px rgba(215,58,73,0.18);
            white-space: nowrap;
            cursor: default;
        }

        /* View Tabs + Filter (案件一覧 / フリーランス一覧 / 未読のみ) */
        .view-tabs {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin: 0.75rem 0 1.25rem;
            flex-wrap: wrap;
        }
        .tab-group { display: flex; gap: 0.75rem; }
        .view-tab {
            padding: 0.6rem 1.15rem;
            border-radius: 999px;
            font-size: 1.17rem;
            font-weight: 900;
            cursor: pointer;
            background: #e1e4e8;
            color: #24292e;
            border: none;
        }
        .view-tab.active {
            background: #0366d6;
            color: #fff;
            box-shadow: 0 2px 10px rgba(3, 102, 214, 0.25);
        }
        .filter-btn {
            font-size: 1.1rem;
            font-weight: 900;
            padding: 0.55rem 1rem;
            border-radius: 999px;
            border: 1px solid #d1d5da;
            background: #fff;
            cursor: pointer;
            white-space: nowrap;
        }
        .filter-btn.active { background: #24292e; color: #fff; border-color: #24292e; }

        /* Layout (案件一覧 + 応募者一覧) */
        .layout { display: none; gap: 1.5rem; align-items: flex-start; }
        .layout.active { display: flex; }
        .jobs { width: 70%; min-width: 0; }
        .applicants {
            width: 30%;
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 14px;
            padding: 1.25rem;
            position: sticky;
            top: calc(var(--header-height) + 64px);
        }
        .applicants-title {
            font-size: 1rem;
            font-weight: 900;
            margin-bottom: 0.75rem;
            color: #24292e;
            letter-spacing: -0.01em;
        }

        /* Job card (案件カード) */
        .job-card {
            background-color: white;
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
            border: 1px solid #e1e4e8;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
            cursor: pointer;
        }
        .job-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        .job-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08); }
        .job-title {
            font-size: 1.56rem;
            font-weight: 900;
            color: #0060ff;
            line-height: 1.35;
            margin-bottom: 0.25rem;
        }
        .job-sub {
            color: #586069;
            font-weight: 700;
            font-size: 1.24rem;
            margin-bottom: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .job-meta {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            color: #24292e;
            font-weight: 800;
            font-size: 1.11rem;
        }
        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
            gap: 0.75rem;
        }

        /* Badges (未読など) */
        .badge-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            background: #d73a49;
            color: #fff;
            border-radius: 999px;
            padding: 0.28rem 0.65rem;
            font-size: 1.01rem;
            font-weight: 900;
            white-space: nowrap;
        }
        .badge-pill.gray { background: #6a737d; }

        /* Freelancer row (応募者行) */
        .freelancer-row {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0;
            border: none;
        }
        .freelancer-link {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .freelancer-link:hover {
            text-decoration: none;
            color: inherit;
        }
        .freelancer-body { flex: 1; min-width: 0; }
        .freelancer-name {
            font-weight: 900;
            font-size: 1.24rem;
            color: #24292e;
            display: flex;
            gap: 0.5rem;
            align-items: baseline;
            flex-wrap: wrap;
        }
        .job-label {
            font-weight: 900;
            font-size: 1.04rem;
            color: #6a737d;
            white-space: nowrap;
        }
        .message {
            font-size: 1.14rem;
            color: #24292e;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .time {
            font-size: 1.01rem;
            color: #6a737d;
            margin-top: 0.15rem;
            white-space: nowrap;
        }
        .freelancer-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .chat-btn {
            background: #0366d6;
            color: #fff;
            padding: 0.28rem 0.65rem;
            border-radius: 10px;
            font-size: 1.04rem;
            font-weight: 900;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            min-width: 78px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .chat-btn.is-disabled { opacity: 0.55; cursor: not-allowed; pointer-events: none; }

        /* Status pill / select (既存更新機能を維持しつつ小さく表示) */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            font-weight: 900;
            font-size: 1.01rem;
            border: 1px solid #e1e4e8;
            background: #fafbfc;
            color: #24292e;
            white-space: nowrap;
        }
        .status-select {
            background: transparent;
            border: none;
            font-weight: 900;
            color: inherit;
            cursor: pointer;
        }

        /* Freelancer view */
        #freelancerView { display: none; }
        .empty-state { text-align: center; padding: 3rem 1rem; color: #586069; }
        .tabs {
            display: inline-flex;
            gap: 0.5rem;
            padding: 0.5rem;
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 1.75rem;
        }
        .tab {
            border: 1px solid #e1e4e8;
            background: #fafbfc;
            color: #24292e;
            border-radius: 10px;
            padding: 0.65rem 1rem;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-block;
        }
        .tab:hover { background: #f6f8fa; transform: translateY(-1px); }
        .tab.is-active {
            background-color: #0366d6;
            border-color: #0366d6;
            color: #fff;
            box-shadow: 0 2px 8px rgba(3, 102, 214, 0.25);
        }

        .list { display: grid; gap: 1.5rem; }
        .card {
            background-color: white;
            border-radius: 14px;
            padding: 1.25rem;
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
        .top {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .title { font-size: 24px; font-weight: 700; color: #0060ff; margin-bottom: 0.5rem; line-height: 1.3; }
        .sub { color: #586069; font-weight: 500; font-size: 18px; }
        .row { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
        .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 900;
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        }
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
            color: #24292e;
            white-space: nowrap;
        }
        .pill.unread { background: #fff5f5; border-color: #ffccd2; color: #b31d28; }
        .pill.status { background: #f6f8fa; }
        .meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            max-width: 500px;
        }
        .meta-item {
            padding: 0.85rem;
            background-color: #f6f8fa;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
        }
        .meta-label { font-size: 16px; color: #6a737d; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .meta-value { font-weight: 900; color: #24292e; white-space: nowrap; font-size: 1.05rem; }
        .skills-section {
            margin-top: 1rem;
            padding: 0.85rem;
            background-color: #f6f8fa;
            border-radius: 10px;
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
        .desc { color: #586069; margin-top: 0.75rem; line-height: 1.65; font-size: 1rem; }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.25rem;
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
        .btn-primary { background-color: #0366d6; color: #fff; font-size: 20px; padding: 15px 60px; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }
        .btn-secondary { background-color: #586069; color: #fff; font-size: 20px; padding: 15px 60px; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }

        @media (max-width: 920px) {
            .header-content { height: var(--header-height-mobile); }
            .nav-links { position: static; left: auto; transform: none; justify-content: flex-start; }
            .user-menu { position: static; transform: none; margin-left: auto; }
            .main-content { padding: 1.5rem; }
            .meta { grid-template-columns: 1fr; }
            .actions .btn { width: 100%; }
            .layout { flex-direction: column; }
            .jobs, .applicants { width: 100%; }
            .applicants { position: static; top: auto; }
        }
        @media (max-width: 1200px) {
            .nav-links { gap: 1rem; }
            .nav-link { font-size: 0.95rem; padding: 0.6rem 0.9rem; }
            .nav-link.has-badge { padding-right: 2.6rem; }
        }
    /* Centered tab bar styling to mimic freelancer view (inside style tag) */
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
    .tab-link:hover { color: #24292e; background-color: #f6f8fa; }
    .tab-link.active {
        color: #0366d6;
        border-bottom-color: #0366d6;
        background-color: transparent;
    }
    </style>
    @include('partials.aitech-responsive')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <nav class="nav-links">
                <a href="{{ route('company.freelancers.index') }}" class="nav-link">フリーランス一覧</a>
                <a href="{{ route('company.jobs.index') }}" class="nav-link">案件一覧</a>
                @php
                    $appUnread = ($unreadApplicationCount ?? 0);
                    $scoutUnread = ($unreadScoutCount ?? 0);
                @endphp
                <a href="{{ route('company.applications.index') }}" class="nav-link {{ $appUnread > 0 ? 'has-badge' : '' }} active">
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
    <!-- Tabs Bar -->
    <div class="tabs-bar">
        <div class="tabs-container">
            <nav aria-label="応募一覧タブ">
                <a class="tab-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('company.applications.index', ['status' => 'pending']) }}" data-tab="active" id="tab-active">応募中</a>
                <a class="tab-link {{ $status === 'closed' ? 'active' : '' }}" href="{{ route('company.applications.index', ['status' => 'closed']) }}" data-tab="closed" id="tab-closed">終了</a>
            </nav>
        </div>
    </div>
    <!-- Main Content -->
    <main class="main-content">
        <div class="content-area">
            @php
                $applicationItems = $applications instanceof \Illuminate\Pagination\AbstractPaginator
                    ? $applications->getCollection()
                    : collect($applications);

                $totalUnreadOnPage = $applicationItems->filter(fn ($a) => (bool)($a->is_unread ?? false))->count();

                $jobGroups = $applicationItems
                    ->groupBy(function ($application) {
                        return optional($application->job)->id ?? ('unknown-' . $application->id);
                    })
                    ->map(function ($group, $jobId) {
                        $first = $group->first();
                        $job = $first->job ?? null;
                        $company = $job->company ?? null;
                        $unreadCount = $group->filter(fn ($a) => (bool)($a->is_unread ?? false))->count();
                        $lastAtTs = $group->max(function ($a) {
                            $dt = $a->created_at ?? $a->updated_at ?? null;
                            return $dt ? $dt->getTimestamp() : 0;
                        });

                        return (object)[
                            'key' => 'job_' . $jobId,
                            'job' => $job,
                            'company' => $company,
                            'applications' => $group,
                            'unreadCount' => $unreadCount,
                            'lastAtTs' => $lastAtTs,
                        ];
                    })
                    ->sortByDesc('lastAtTs')
                    ->values();

                $freelancerRows = $applicationItems
                    ->sortByDesc(function ($a) {
                        $dt = $a->created_at ?? $a->updated_at ?? null;
                        return $dt ? $dt->getTimestamp() : 0;
                    })
                    ->values();
            @endphp

            <div class="view-tabs" aria-label="表示切替">
                <span class="total-unread" id="totalUnread" data-total-unread="{{ $totalUnreadOnPage }}">未読 {{ $totalUnreadOnPage }}</span>
                <div class="tab-group" role="tablist" aria-label="表示タブ">
                    <button type="button" class="view-tab active" id="tabJobs" data-view-tab="jobs" aria-selected="true">案件一覧</button>
                    <button type="button" class="view-tab" id="tabFreelancers" data-view-tab="freelancers" aria-selected="false">フリーランス一覧</button>
                </div>
                <button type="button" class="filter-btn" id="filterBtn" aria-pressed="false">未読のみ</button>
            </div>

            @if($jobGroups->count() === 0)
                <div class="empty-state">
                    <p style="font-size: 1.05rem; font-weight: 900;">応募がありません</p>
                </div>
            @else
                <div class="layout active" id="jobView">
                    <section class="jobs" id="jobs" aria-label="案件一覧">
                        @foreach($jobGroups as $group)
                            @php
                                $job = $group->job;
                                $company = $group->company;

                                $rewardText = '';
                                if ($job && $job->reward_type === 'monthly') {
                                    $rewardText = number_format($job->min_rate / 10000, 0) . '〜' . number_format($job->max_rate / 10000, 0) . '万';
                                } elseif ($job) {
                                    $rewardText = number_format($job->min_rate) . '〜' . number_format($job->max_rate) . '円/時';
                                }
                                $workTimeText = $job->work_time_text ?? '';
                            @endphp

                            <div class="job-card" data-job-key="{{ $group->key }}" data-unread-count="{{ $group->unreadCount }}" role="button" tabindex="0">
                                <div class="job-title">{{ $job->title ?? '案件名不明' }}</div>
                                <div class="job-sub">{{ $company->name ?? '企業名不明' }}</div>
                                <div class="job-meta">
                                    @if($rewardText)<span>報酬：{{ $rewardText }}</span>@endif
                                    @if($workTimeText)<span>稼働：{{ $workTimeText }}</span>@endif
                                </div>
                                <div class="job-footer">
                                    <span style="font-weight:900;">応募者 {{ $group->applications->count() }}名</span>
                                    <span class="badge-pill {{ $group->unreadCount === 0 ? 'gray' : '' }}">未読 {{ $group->unreadCount }}</span>
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <aside class="applicants" id="applicants" aria-label="応募者一覧">
                        @foreach($jobGroups as $group)
                            @php $job = $group->job; @endphp
                            <div class="job-applicants" data-job-key="{{ $group->key }}" style="display:none;">
                                <div class="applicants-title">{{ $job->title ?? '案件名不明' }}</div>
                                @foreach($group->applications as $application)
                                    @php
                                        $freelancer = $application->freelancer;
                                        $freelancerInitial = mb_substr($freelancer->display_name ?? '未', 0, 1);
                                        $dt = $application->created_at ?? $application->updated_at ?? null;
                                        $timeText = $dt ? $dt->format('Y/m/d H:i') : '';

                                        $statusText = '';
                                        if ($application->status === \App\Models\Application::STATUS_PENDING) {
                                            $statusText = '未対応';
                                        } elseif ($application->status === \App\Models\Application::STATUS_IN_PROGRESS) {
                                            $statusText = '対応中';
                                        } else {
                                            $statusText = 'クローズ';
                                        }

                                        $chatUrl = $application->thread
                                            ? route('company.threads.show', ['thread' => $application->thread])
                                            : null;
                                    @endphp

                                    @if($chatUrl)
                                        <a href="{{ $chatUrl }}" class="freelancer-row freelancer-link" data-unread="{{ ($application->is_unread ?? false) ? '1' : '0' }}" data-job-key="{{ $group->key }}">
                                    @else
                                        <div class="freelancer-row" data-unread="{{ ($application->is_unread ?? false) ? '1' : '0' }}" data-job-key="{{ $group->key }}">
                                    @endif
                                        <div class="avatar" aria-hidden="true">{{ $freelancerInitial }}</div>
                                        <div class="freelancer-body">
                                            <div class="freelancer-name">
                                                <span>{{ $freelancer->display_name ?? '名前不明' }}</span>

                                                @if($status === 'pending')
                                                    @if($application->status === \App\Models\Application::STATUS_PENDING)
                                                        <form method="POST" action="{{ route('company.applications.update', ['application' => $application->id]) }}" style="display:inline;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <label class="status-pill">
                                                                応募：
                                                                <select name="status" class="status-select" onchange="this.form.submit()">
                                                                    <option value="{{ \App\Models\Application::STATUS_PENDING }}" selected>未対応</option>
                                                                    <option value="{{ \App\Models\Application::STATUS_IN_PROGRESS }}">対応中</option>
                                                                    <option value="{{ \App\Models\Application::STATUS_CLOSED }}">クローズ</option>
                                                                </select>
                                                            </label>
                                                        </form>
                                                    @else
                                                        <span class="status-pill">応募：{{ $statusText }}</span>
                                                    @endif
                                                @else
                                                    <span class="status-pill">応募：{{ $statusText }}</span>
                                                @endif
                                            </div>
                                            <div class="message">{{ Str::limit($application->message ?? ($job->description ?? ''), 38) }}</div>
                                            <div class="time">{{ $timeText }}</div>
                                            <div class="freelancer-actions" aria-label="操作">
                                                @if($application->is_unread ?? false)
                                                    <span class="badge-pill">未読</span>
                                                @endif
                                                <span class="chat-btn {{ $chatUrl ? '' : 'is-disabled' }}">チャット</span>
                                            </div>
                                        </div>
                                    @if($chatUrl)
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </aside>
                </div>

                <div id="freelancerView" aria-label="フリーランス一覧">
                    <div class="list">
                        @foreach($freelancerRows as $application)
                            @php
                                $job = $application->job;
                                $freelancer = $application->freelancer;
                                $jobGroupKey = 'job_' . (optional($job)->id ?? ('unknown-' . $application->id));

                                $freelancerInitial = mb_substr($freelancer->display_name ?? '未', 0, 1);
                                $dt = $application->created_at ?? $application->updated_at ?? null;
                                $timeText = $dt ? $dt->format('Y/m/d H:i') : '';
                                $chatUrl = $application->thread
                                    ? route('company.threads.show', ['thread' => $application->thread])
                                    : null;
                            @endphp
                            <div class="card">
                                @if($chatUrl)
                                    <a href="{{ $chatUrl }}" class="freelancer-row freelancer-link" data-unread="{{ ($application->is_unread ?? false) ? '1' : '0' }}" data-job-key="{{ $jobGroupKey }}">
                                @else
                                    <div class="freelancer-row" data-unread="{{ ($application->is_unread ?? false) ? '1' : '0' }}" data-job-key="{{ $jobGroupKey }}">
                                @endif
                                    <div class="avatar" aria-hidden="true">{{ $freelancerInitial }}</div>
                                    <div class="freelancer-body">
                                        <div class="freelancer-name">
                                            <span>{{ $freelancer->display_name ?? '名前不明' }}</span>
                                            <span class="job-label">案件：{{ $job->title ?? '案件名不明' }}</span>
                                        </div>
                                        <div class="message">{{ Str::limit($application->message ?? ($job->description ?? ''), 44) }}</div>
                                        <div class="time">{{ $timeText }}</div>
                                        <div class="freelancer-actions" aria-label="操作">
                                            @if($application->is_unread ?? false)
                                                <span class="badge-pill">未読</span>
                                            @endif
                                            <span class="chat-btn {{ $chatUrl ? '' : 'is-disabled' }}">チャット</span>
                                        </div>
                                    </div>
                                @if($chatUrl)
                                    </a>
                                @else
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($applications->hasPages())
                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
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
            const tabJobs = document.getElementById('tabJobs');
            const tabFreelancers = document.getElementById('tabFreelancers');
            const jobView = document.getElementById('jobView');
            const freelancerView = document.getElementById('freelancerView');
            const filterBtn = document.getElementById('filterBtn');
            const totalUnreadEl = document.getElementById('totalUnread');

            // 応募が無いケースなど、要素が無ければ何もしない
            if (!tabJobs || !tabFreelancers || !jobView || !freelancerView || !filterBtn) return;

            let unreadOnly = false;
            let currentJobKey = null;

            const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));
            const jobCards = () => qsa('.job-card');
            const jobApplicantsPanels = () => qsa('.job-applicants');
            const allFreelancerRows = () => qsa('.freelancer-row');

            function setActiveJob(jobKey) {
                currentJobKey = jobKey;
                jobCards().forEach(c => c.classList.toggle('active', c.dataset.jobKey === jobKey));
                jobApplicantsPanels().forEach(p => {
                    p.style.display = (p.dataset.jobKey === jobKey ? '' : 'none');
                });
                applyUnreadFilter();
            }

            function firstVisibleJobKey() {
                const visible = jobCards().find(c => c.style.display !== 'none');
                return visible ? visible.dataset.jobKey : null;
            }

            function applyUnreadFilter() {
                // 案件カード
                jobCards().forEach(card => {
                    const unreadCount = Number(card.dataset.unreadCount || 0);
                    const hide = unreadOnly && unreadCount === 0;
                    card.style.display = hide ? 'none' : '';
                });

                // アクティブ案件が隠れたら、先頭の表示案件を選択
                if (currentJobKey) {
                    const activeCard = jobCards().find(c => c.dataset.jobKey === currentJobKey);
                    if (!activeCard || activeCard.style.display === 'none') {
                        const nextKey = firstVisibleJobKey();
                        if (nextKey) setActiveJob(nextKey);
                    }
                }

                // 応募者行（両ビュー共通）
                allFreelancerRows().forEach(row => {
                    const isUnread = row.dataset.unread === '1';
                    row.style.display = (unreadOnly && !isUnread) ? 'none' : '';
                });

                // 右側（現在の案件）の0件表示
                if (jobView.classList.contains('active') && currentJobKey) {
                    const panel = jobApplicantsPanels().find(p => p.dataset.jobKey === currentJobKey);
                    if (panel) {
                        const rows = qsa('.freelancer-row', panel).filter(r => r.style.display !== 'none');
                        const existingEmpty = panel.querySelector('[data-empty="1"]');
                        if (existingEmpty) existingEmpty.remove();
                        if (rows.length === 0) {
                            const empty = document.createElement('div');
                            empty.className = 'empty-state';
                            empty.dataset.empty = '1';
                            empty.style.padding = '1.5rem 0.5rem';
                            empty.innerHTML = '<p style="font-weight:900;">該当する応募がありません</p>';
                            panel.appendChild(empty);
                        }
                    }
                }

                // 上部の総未読（ページ内）表示は固定値（サーバー計算）でOK
                if (totalUnreadEl) {
                    const total = totalUnreadEl.dataset.totalUnread || '0';
                    totalUnreadEl.textContent = `未読 ${total}`;
                }
            }

            function showJobs() {
                tabJobs.classList.add('active');
                tabFreelancers.classList.remove('active');
                tabJobs.setAttribute('aria-selected', 'true');
                tabFreelancers.setAttribute('aria-selected', 'false');
                jobView.classList.add('active');
                freelancerView.style.display = 'none';
                applyUnreadFilter();
            }

            function showFreelancers() {
                tabFreelancers.classList.add('active');
                tabJobs.classList.remove('active');
                tabFreelancers.setAttribute('aria-selected', 'true');
                tabJobs.setAttribute('aria-selected', 'false');
                jobView.classList.remove('active');
                freelancerView.style.display = 'block';
                applyUnreadFilter();
            }

            tabJobs.addEventListener('click', showJobs);
            tabFreelancers.addEventListener('click', showFreelancers);

            filterBtn.addEventListener('click', () => {
                unreadOnly = !unreadOnly;
                filterBtn.classList.toggle('active', unreadOnly);
                filterBtn.setAttribute('aria-pressed', unreadOnly ? 'true' : 'false');
                applyUnreadFilter();
            });

            // 案件選択（クリック + Enter/Space）
            jobCards().forEach(card => {
                const key = card.dataset.jobKey;
                const onSelect = () => setActiveJob(key);
                card.addEventListener('click', onSelect);
                card.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        onSelect();
                    }
                });
            });

            // 初期：先頭案件を選択して案件一覧表示
            const initKey = firstVisibleJobKey();
            if (initKey) setActiveJob(initKey);
            showJobs();
        })();
    </script>
</body>
</html>
