<header class="header">
    <div class="header-content">
        <div class="header-left">
            <button class="nav-toggle" id="mobileNavToggle" type="button" aria-label="メニューを開く" aria-haspopup="menu" aria-expanded="false" aria-controls="mobileNav">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <div class="logo" aria-hidden="true">
                <div class="logo-text">複業AI</div>
            </div>
        </div>

        <nav class="nav-links" id="desktopNav" aria-label="グローバルナビゲーション">
            <a href="{{ route('company.freelancers.index') }}" class="nav-link {{ request()->routeIs('company.freelancers.*') ? 'active' : '' }}">フリーランス一覧</a>
            <a href="{{ route('company.jobs.index') }}" class="nav-link {{ request()->routeIs('company.jobs.*') ? 'active' : '' }}">案件一覧</a>
            @php
                $appUnread = ($unreadApplicationCount ?? 0);
                $scoutUnread = ($unreadScoutCount ?? 0);
            @endphp
            <a href="{{ route('company.applications.index') }}" class="nav-link {{ ($appUnread > 0 ? 'has-badge' : '') }} {{ (request()->routeIs('company.applications.*') || request()->routeIs('company.threads.*') || request()->routeIs('company.messages.*')) ? 'active' : '' }}">
                応募された案件
                @if($appUnread > 0)
                    <span class="badge">{{ $appUnread }}</span>
                @endif
            </a>
            <a href="{{ route('company.scouts.index') }}" class="nav-link {{ ($scoutUnread > 0 ? 'has-badge' : '') }} {{ request()->routeIs('company.scouts.*') ? 'active' : '' }}">
                スカウト
                @if($scoutUnread > 0)
                    <span class="badge">{{ $scoutUnread }}</span>
                @endif
            </a>
        </nav>

        <div class="header-right">
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
    </div>
    <div class="mobile-nav" id="mobileNav" role="menu" aria-label="メニュー">
        <div class="mobile-nav-inner" id="mobileNavInner"></div>
    </div>
    <script>
        (function () {
            const header = document.querySelector('header.header');
            const toggleBtn = document.getElementById('mobileNavToggle');
            const mobileNav = document.getElementById('mobileNav');
            const mobileNavInner = document.getElementById('mobileNavInner');
            const desktopNav = document.getElementById('desktopNav');

            if (!header || !toggleBtn || !mobileNav || !mobileNavInner || !desktopNav) return;

            mobileNavInner.innerHTML = desktopNav.innerHTML;

            const open = () => {
                header.classList.add('is-mobile-nav-open');
                toggleBtn.setAttribute('aria-expanded', 'true');
                toggleBtn.setAttribute('aria-label', 'メニューを閉じる');
            };
            const close = () => {
                header.classList.remove('is-mobile-nav-open');
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.setAttribute('aria-label', 'メニューを開く');
            };
            const isOpen = () => header.classList.contains('is-mobile-nav-open');

            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                isOpen() ? close() : open();
            });

            mobileNav.addEventListener('click', (e) => e.stopPropagation());

            document.addEventListener('click', () => { if (isOpen()) close(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen()) close(); });

            const mq = window.matchMedia('(min-width: 768px)');
            const onChange = () => { if (mq.matches) close(); };
            if (mq.addEventListener) mq.addEventListener('change', onChange);
            else mq.addListener(onChange);
        })();
    </script>
</header>
