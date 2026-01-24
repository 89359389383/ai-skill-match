<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリーランス詳細（企業）- AITECH</title>
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

        .main-content { max-width: 1600px; margin: 0 auto; padding: 3rem; }
        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: #24292e;
            letter-spacing: -0.025em;
        }
        .grid { display: grid; grid-template-columns: 1fr 420px; gap: 2rem; }
        .panel {
            background-color: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            border: 1px solid #e1e4e8;
        }
        .row { display: flex; gap: 1rem; align-items: flex-start; }
        .avatar {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 900; box-shadow: 0 2px 10px rgba(0,0,0,0.12);
            flex: 0 0 auto;
        }
        .name { font-size: 1.6rem; font-weight: 900; line-height: 1.2; margin-bottom: 0.25rem; }
        .sub { color: #586069; font-weight: 700; font-size: 1rem; }
        .desc { color: #586069; margin-top: 0.9rem; line-height: 1.7; }
        .title { font-size: 1.05rem; font-weight: 900; margin-top: 1.5rem; margin-bottom: 0.6rem; }
        .tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.6rem; }
        .tag { background-color: #f1f8ff; color: #0366d6; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.85rem; font-weight: 800; border: 1px solid #c8e1ff; }
        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .meta-item {
            padding: 0.9rem;
            background-color: #f6f8fa;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
        }
        .meta-label { font-size: 0.75rem; color: #6a737d; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .meta-value { font-weight: 900; color: #24292e; white-space: nowrap; }
        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 8px;
            font-weight: 800;
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
        .btn-primary { background-color: #0366d6; color: white; }
        .btn-primary:hover { background-color: #0256cc; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(3, 102, 214, 0.3); }
        .btn-secondary { background-color: #586069; color: white; }
        .btn-secondary:hover { background-color: #4c5561; transform: translateY(-1px); }
        .btn-row { display: flex; gap: 0.75rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .link { color: #0366d6; text-decoration: none; font-weight: 900; }
        .link:hover { text-decoration: underline; }

        @media (max-width: 920px) {
            .header-content { height: var(--header-height-mobile); }
            .nav-links { position: static; left: auto; transform: none; justify-content: flex-start; }
            .user-menu { position: static; transform: none; margin-left: auto; }
            .main-content { padding: 1.5rem; }
            .grid { grid-template-columns: 1fr; }
            .meta { grid-template-columns: 1fr; }
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
                <a href="{{ route('company.freelancers.index') }}" class="nav-link active">フリーランス一覧</a>
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
        <h1 class="page-title">フリーランス詳細</h1>
        <div class="grid">
            <section class="panel">
                <div class="row">
                    <div class="avatar" aria-hidden="true">山</div>
                    <div style="min-width:0;">
                        <div class="name">山田 太郎</div>
                        <div class="sub">フルスタックエンジニア（Laravel / Vue）</div>
                        <div class="desc">
                            希望：週20〜30時間 / リモート中心 / 業務系・ECの改善案件に強み
                        </div>
                    </div>
                </div>

                <div class="title">自己紹介</div>
                <div class="desc">
                    受託・自社の両方で、要件整理から設計・実装・運用改善まで担当してきました。
                    MVPの最短実装と、後から拡張しやすい設計のバランスを重視します。
                </div>

                <div class="title">スキル</div>
                <div class="tags" aria-label="スキル一覧">
                    <span class="tag">PHP</span>
                    <span class="tag">Laravel</span>
                    <span class="tag">Vue.js</span>
                    <span class="tag">JavaScript</span>
                    <span class="tag">MySQL</span>
                    <span class="tag">Docker</span>
                </div>

                <div class="title">経験企業</div>
                <div class="desc">
                    - 株式会社A（ECリニューアル）<br>
                    - 株式会社B（在庫管理・受発注システム）<br>
                    - スタートアップC（SaaS管理画面）
                </div>

                <div class="title">ワークスタイル</div>
                <div class="meta">
                    <div class="meta-item"><div class="meta-label">稼働</div><div class="meta-value">週20〜30h</div></div>
                    <div class="meta-item"><div class="meta-label">働き方</div><div class="meta-value">リモート</div></div>
                </div>

                <div class="title">希望単価</div>
                <div class="meta">
                    <div class="meta-item"><div class="meta-label">下限</div><div class="meta-value">60万</div></div>
                    <div class="meta-item"><div class="meta-label">上限</div><div class="meta-value">80万</div></div>
                </div>

                <div class="title">ポートフォリオ</div>
                <div class="desc">
                    <a class="link" href="#" onclick="return false;">https://portfolio.example.com/yamada</a><br>
                    <a class="link" href="#" onclick="return false;">https://github.example.com/yamada</a>
                </div>
            </section>

            <aside class="panel">
                <div class="title" style="margin-top:0;">アクション</div>
                <div class="desc">
                    このフリーランスにスカウトを送ると、<strong>企業 × フリーランス</strong> のチャットスレッドが自動生成されます。
                </div>
                <div class="btn-row">
                    <a class="btn btn-primary" href="#">スカウト</a>
                    <a class="btn btn-secondary" href="#">一覧へ戻る</a>
                </div>
                <div class="title">メモ</div>
                <div class="desc">
                    - 直近：EC改善の実績あり<br>
                    - コミュニケーション：早い返信<br>
                    - 希望：週20〜30h
                </div>
            </aside>
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
