<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール登録 - AITECH</title>
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
        @media (max-width: 768px) {
            .header-content { padding: 0 1.5rem; height: var(--header-height-mobile); }
            .nav-links { gap: 1.5rem; position: static; left: auto; transform: none; justify-content: flex-start; flex-direction: row; flex-wrap: wrap; }
            .user-menu { position: static; right: auto; top: auto; transform: none; margin-left: auto; }
            .nav-link { padding: 0.5rem 1rem; font-size: 1rem; }
        }
    </style>
    <style>
        /* 元の create.blade.php の CSS を保持します */
        :root {
            --header-height: 104px;
            --header-height-mobile: 91px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 97.5%; }
        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #fafbfc;
            color: #24292e;
            line-height: 1.5;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }
        .content-area {
            width: 100%;
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
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
            margin-bottom: 2rem;
        }
        .panel-title {
            font-size: 1.1rem;
            font-weight: 900;
            margin-bottom: 1.25rem;
            color: #24292e;
            letter-spacing: -0.01em;
        }

        .form {
            display: grid;
            gap: 1.5rem;
        }
        .form-row {
            display: grid;
            gap: 0.75rem;
        }
        .label {
            font-weight: 900;
            color: #586069;
            font-size: 0.9rem;
        }
        .required {
            color: #d73a49;
            font-weight: 800;
        }

        .input, .textarea, .select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e1e4e8;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            background-color: #fafbfc;
        }
        .input:focus, .textarea:focus, .select:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
            background-color: white;
        }
        .input.is-invalid, .textarea.is-invalid {
            border-color: #d73a49;
        }
        .textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.6;
        }
        .error-message {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 800;
            color: #dc2626;
        }

        .file-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px dashed #e1e4e8;
            border-radius: 10px;
            background-color: #fafbfc;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .file-input:hover {
            border-color: #0366d6;
            background-color: #f6f8fa;
        }
        .file-input:focus {
            outline: none;
            border-color: #0366d6;
            box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.1);
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid #e1e4e8;
            flex-wrap: wrap;
        }
        .btn {
            padding: 15px 60px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            font-size: 20px;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }
        .btn-primary { background-color: #0366d6; color: white; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }
        .btn-secondary { background-color: #586069; color: white; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content { padding: 0 1.5rem; height: var(--header-height-mobile); }
            .nav-links { gap: 1.5rem; position: static; left: auto; transform: none; justify-content: flex-start; flex-direction: row; flex-wrap: wrap; }
            .user-menu { position: static; right: auto; top: auto; transform: none; margin-left: auto; }
            .nav-link { padding: 0.5rem 1rem; font-size: 1rem; }
            .main-content { padding: 1.5rem; }
            .panel { padding: 1.5rem; }
            .actions { flex-direction: column; }
            .btn { width: 100%; }
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
            <h1 class="page-title">プロフィール登録</h1>
            @include('partials.error-panel')
            <p class="page-subtitle">フリーランスプロフィールを作成します。</p>

            <!-- フォーム -->
            <section class="panel">
                <form action="{{ route('freelancer.profile.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row">
                        <label class="label" for="display_name">表示名 <span class="required">必須</span></label>
                        <input type="text" id="display_name" name="display_name" class="input @error('display_name') is-invalid @enderror" value="{{ old('display_name') }}" placeholder="例: 山田太郎">
                        @error('display_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label class="label" for="job_title">職種・肩書 <span class="required">必須</span></label>
                        <input type="text" id="job_title" name="job_title" class="input @error('job_title') is-invalid @enderror" value="{{ old('job_title') }}" placeholder="例: Webエンジニア / フロントエンド開発">
                        @error('job_title')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label class="label" for="introduction">自己紹介 <span class="required">必須</span></label>
                        <textarea id="introduction" name="introduction" class="textarea @error('introduction') is-invalid @enderror" placeholder="あなたの経験や得意分野について教えてください">{{ old('introduction') }}</textarea>
                        @error('introduction')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label class="label" for="skills">スキル <span class="required">必須</span></label>
                        <input type="text" id="skills" name="skills" class="input @error('skills') is-invalid @enderror" value="{{ old('skills') }}" placeholder="例: PHP, Laravel, JavaScript, Vue.js (カンマ区切り)">
                        @error('skills')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label class="label" for="icon">プロフィール画像</label>
                        <input type="file" id="icon" name="icon" class="file-input @error('icon') is-invalid @enderror" accept="image/*">
                        @error('icon')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="actions">
                        <button class="btn btn-primary" type="submit">プロフィールを作成</button>
                    </div>
                </form>
            </section>
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