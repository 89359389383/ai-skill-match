@php
    $appUnread = $unreadApplicationCount ?? 0;
    $scoutUnread = $unreadScoutCount ?? 0;
    $salesCount = $salesOrderCount ?? 0;
    $userDisplayName = $freelancer?->display_name ?? 'ゲストユーザー';
    $userIcon = $freelancer?->icon_path ?? null;
    $avatarSrc = !empty($userIcon) ? (str_starts_with($userIcon, 'http') ? $userIcon : asset('storage/' . $userIcon)) : null;
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
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="プロフィール画像" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            {{ $userInitial ?? 'U' }}
                        @endif
                    </button>
                    <div class="dropdown-content" id="freelancerUserDropdownMenu" role="menu" aria-label="ユーザーメニュー">
                        <div class="dropdown-profile">
                            @if($avatarSrc)
                                <img src="{{ $avatarSrc }}" alt="{{ $userDisplayName }}" class="dropdown-profile-avatar">
                            @else
                                <div class="dropdown-profile-avatar-initial">{{ $userInitial ?? 'U' }}</div>
                            @endif
                            <div class="dropdown-profile-info">
                                <div class="dropdown-profile-name">{{ $userDisplayName }}</div>
                                <div class="dropdown-profile-role">フリーランス</div>
                            </div>
                        </div>
                        <div class="dropdown-nav">
                            <a href="{{ route('profiles.show', auth('freelancer')->user()) }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="dropdown-item-text">プロフィール</span>
                            </a>
                            <a href="{{ route('purchased-skills.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="dropdown-item-text">購入したスキル</span>
                            </a>
                            <a href="{{ route('sales-performance.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="dropdown-item-text">販売実績</span>
                                @if($salesCount > 0)
                                    <span class="dropdown-item-badge dropdown-item-badge-green">{{ $salesCount }}件</span>
                                @endif
                            </a>
                            <a href="{{ route('freelancer.jobs.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span class="dropdown-item-text">企業案件</span>
                                @if($appUnread > 0)
                                    <span class="dropdown-item-badge dropdown-item-badge-blue">新着{{ $appUnread }}</span>
                                @endif
                            </a>
                            <a href="{{ route('freelancer.scouts.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                <span class="dropdown-item-text">スカウト</span>
                                @if($scoutUnread > 0)
                                    <span class="dropdown-item-badge dropdown-item-badge-purple">{{ $scoutUnread }}件</span>
                                @endif
                            </a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-nav">
                            <a href="{{ route('freelancer.profile.settings') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="dropdown-item-text">設定</span>
                            </a>
                            <form method="POST" action="{{ route('auth.logout') }}" class="dropdown-item-form">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-item-logout" role="menuitem">
                                    <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span class="dropdown-item-text">ログアウト</span>
                                </button>
                            </form>
                        </div>
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
