@php
    $appUnread = $unreadApplicationCount ?? 0;
    $scoutUnread = $unreadScoutCount ?? 0;
@endphp
<header class="header header-role" role="banner">
    <div class="header-content">
        <div class="header-left">
            <div class="logo" aria-hidden="true">
                <div class="logo-text">複業AI</div>
            </div>
        </div>

        <nav class="nav-links" role="navigation" aria-label="フリーランスナビゲーション">
            <a href="{{ route('freelancer.jobs.index') }}" class="nav-link {{ request()->routeIs('freelancer.jobs.*') ? 'active' : '' }}">案件一覧</a>
            <a href="{{ route('sales-performance.index') }}" class="nav-link {{ request()->routeIs('sales-performance.*') || request()->routeIs('transactions.*') ? 'active' : '' }}">販売実績</a>
            <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ request()->routeIs('freelancer.applications.*') ? 'active' : '' }} {{ $appUnread > 0 ? 'has-badge' : '' }}">
                応募した案件
                @if($appUnread > 0)
                    <span class="badge" aria-live="polite">{{ $appUnread }}</span>
                @endif
            </a>
            <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ request()->routeIs('freelancer.scouts.*') ? 'active' : '' }} {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                スカウト
                @if($scoutUnread > 0)
                    <span class="badge" aria-hidden="false">{{ $scoutUnread }}</span>
                @endif
            </a>
        </nav>

        <div class="header-right" role="region" aria-label="ユーザー">
            <button
                class="nav-toggle"
                id="freelancerMobileNavToggle"
                type="button"
                aria-label="メニューを開く"
                aria-haspopup="menu"
                aria-expanded="false"
                aria-controls="freelancerMobileNav"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 6h18"></path>
                    <path d="M3 12h18"></path>
                    <path d="M3 18h18"></path>
                </svg>
            </button>

            <div class="user-menu">
                <div class="dropdown" id="freelancerUserDropdown">
                    <button class="user-avatar" id="freelancerUserDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="freelancerUserDropdownMenu">
                        @if(isset($freelancer) && $freelancer && $freelancer->icon_path)
                            <img src="{{ asset('storage/' . $freelancer->icon_path) }}" alt="プロフィール画像" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            {{ $userInitial ?? 'U' }}
                        @endif
                    </button>
                    <div class="dropdown-content" id="freelancerUserDropdownMenu" role="menu" aria-label="ユーザーメニュー">
                        <a href="{{ route('purchased-skills.index') }}" class="dropdown-item" role="menuitem">購入したスキル</a>
                        <a href="{{ route('sales-performance.index') }}" class="dropdown-item" role="menuitem">購入されたスキル</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profiles.show', auth('freelancer')->user()) }}" class="dropdown-item" role="menuitem">プロフィール詳細</a>
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
    </div>

    <div class="mobile-nav" id="freelancerMobileNav" role="menu" aria-label="モバイルナビゲーション">
        <div class="mobile-nav-inner">
            <a href="{{ route('freelancer.jobs.index') }}" class="nav-link {{ request()->routeIs('freelancer.jobs.*') ? 'active' : '' }}">案件一覧</a>
            <a href="{{ route('sales-performance.index') }}" class="nav-link {{ request()->routeIs('sales-performance.*') || request()->routeIs('transactions.*') ? 'active' : '' }}">販売実績</a>
            <a href="{{ route('freelancer.applications.index') }}" class="nav-link {{ request()->routeIs('freelancer.applications.*') ? 'active' : '' }} {{ $appUnread > 0 ? 'has-badge' : '' }}">
                応募した案件
                @if($appUnread > 0)
                    <span class="badge" aria-live="polite">{{ $appUnread }}</span>
                @endif
            </a>
            <a href="{{ route('freelancer.scouts.index') }}" class="nav-link {{ request()->routeIs('freelancer.scouts.*') ? 'active' : '' }} {{ $scoutUnread > 0 ? 'has-badge' : '' }}">
                スカウト
                @if($scoutUnread > 0)
                    <span class="badge" aria-hidden="false">{{ $scoutUnread }}</span>
                @endif
            </a>
        </div>
    </div>
    <script>
        (function () {
            const header = document.querySelector('header.header.header-role');
            const toggleBtn = document.getElementById('freelancerMobileNavToggle');
            const mobileNav = document.getElementById('freelancerMobileNav');
            const dropdown = document.getElementById('freelancerUserDropdown');
            const dropdownToggle = document.getElementById('freelancerUserDropdownToggle');
            const dropdownMenu = document.getElementById('freelancerUserDropdownMenu');

            if (toggleBtn && mobileNav && header) {
                const open = () => { header.classList.add('is-mobile-nav-open'); toggleBtn.setAttribute('aria-expanded', 'true'); toggleBtn.setAttribute('aria-label', 'メニューを閉じる'); };
                const close = () => { header.classList.remove('is-mobile-nav-open'); toggleBtn.setAttribute('aria-expanded', 'false'); toggleBtn.setAttribute('aria-label', 'メニューを開く'); };
                const isOpen = () => header.classList.contains('is-mobile-nav-open');
                toggleBtn.addEventListener('click', (e) => { e.stopPropagation(); isOpen() ? close() : open(); });
                mobileNav.addEventListener('click', (e) => e.stopPropagation());
                document.addEventListener('click', () => { if (isOpen()) close(); });
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen()) close(); });
                const mq = window.matchMedia('(min-width: 768px)');
                if (mq.addEventListener) mq.addEventListener('change', () => { if (mq.matches) close(); });
                else mq.addListener(() => { if (mq.matches) close(); });
            }

            if (dropdown && dropdownToggle && dropdownMenu) {
                const open = () => { dropdown.classList.add('is-open'); dropdownToggle.setAttribute('aria-expanded', 'true'); };
                const close = () => { dropdown.classList.remove('is-open'); dropdownToggle.setAttribute('aria-expanded', 'false'); };
                const isOpen = () => dropdown.classList.contains('is-open');
                dropdownToggle.addEventListener('click', (e) => { e.stopPropagation(); isOpen() ? close() : open(); });
                document.addEventListener('click', (e) => { if (!dropdown.contains(e.target)) close(); });
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
            }
        })();
    </script>
</header>
