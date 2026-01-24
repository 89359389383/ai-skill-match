<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>企業プロフィール設定 - AITECH</title>
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
        .nav-link.active { background-color: #0366d6; color: white; box-shadow: 0 2px 8px rgba(3, 102, 214, 0.3); }
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
            transition: all 0.15s ease; border-radius: 6px; margin: 0.25rem; white-space: nowrap;
        }
        .dropdown-item:hover { background-color: #f6f8fa; color: #24292e; }
        .dropdown-divider { height: 1px; background-color: #e1e4e8; margin: 0.5rem 0; }

        .main-content { max-width: 700px; margin: 0 auto; padding: 3rem; }
        .page-title { font-size: 2rem; font-weight: 900; margin-bottom: 1.5rem; letter-spacing: -0.025em; }
        .panel {
            background-color: white; border-radius: 16px; padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
        }
        .field { margin-bottom: 1.25rem; }
        .field label { display: block; font-weight: 900; margin-bottom: 0.75rem; color: #586069; font-size: 0.9rem; }
        .input, .textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .textarea { min-height: 140px; resize: vertical; }
        .input:focus, .textarea:focus { outline: none; border-color: #0366d6; box-shadow: 0 0 0 3px rgba(3,102,214,0.1); background-color: #fff; }
        .input.is-invalid, .textarea.is-invalid {
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
        .btn-row { display: flex; gap: 0.75rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .btn-row .btn { flex: 1; }
        .btn {
            padding: 0.875rem 1.75rem; border-radius: 10px; font-weight: 900;
            text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.15s ease; cursor: pointer; border: none; font-size: 0.95rem; white-space: nowrap;
        }
        .btn-primary { background-color: #0366d6; color: #fff; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3,102,214,0.3); }
        .btn-secondary { background-color: #586069; color: #fff; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }

        @media (max-width: 920px) {
            .header-content { height: var(--header-height-mobile); }
            .nav-links { position: static; left: auto; transform: none; justify-content: flex-start; }
            .user-menu { position: static; transform: none; margin-left: auto; }
            .main-content { padding: 1.5rem; }
            .btn-row .btn { width: 100%; }
        }
        @media (max-width: 1200px) {
            .nav-links { gap: 1rem; }
            .nav-link { font-size: 0.95rem; padding: 0.6rem 0.9rem; }
            .nav-link.has-badge { padding-right: 2.6rem; }
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
        <h1 class="page-title">企業プロフィール設定</h1>
        @include('partials.error-panel')
        
        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif


        <div class="panel">
            <form action="{{ route('company.profile.settings.update') }}" method="post">
                @csrf
                <div class="field">
                    <label for="name">企業名（必須）</label>
                    <input id="name" name="name" class="input @error('name') is-invalid @enderror" type="text" value="{{ old('name', $company->name ?? '') }}">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="field">
                    <label for="overview">会社概要（任意）</label>
                    <textarea id="overview" name="overview" class="textarea @error('overview') is-invalid @enderror">{{ old('overview', $company->overview ?? '') }}</textarea>
                    @error('overview')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="field">
                    <label for="contact_name">担当者名（任意）</label>
                    <input id="contact_name" name="contact_name" class="input @error('contact_name') is-invalid @enderror" type="text" value="{{ old('contact_name', $company->contact_name ?? '') }}">
                    @error('contact_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="field">
                    <label for="department">部署（任意）</label>
                    <input id="department" name="department" class="input @error('department') is-invalid @enderror" type="text" value="{{ old('department', $company->department ?? '') }}">
                    @error('department')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="field">
                    <label for="introduction">自己紹介（任意）</label>
                    <textarea id="introduction" name="introduction" class="textarea @error('introduction') is-invalid @enderror">{{ old('introduction', $company->introduction ?? '') }}</textarea>
                    @error('introduction')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="btn-row">
                    <a class="btn btn-secondary" href="{{ route('company.freelancers.index') }}">キャンセル</a>
                    <button class="btn btn-primary" type="submit">更新</button>
                </div>
            </form>
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
</body>
</html>
