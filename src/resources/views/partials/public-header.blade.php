@php
    $isOnRolePage = request()->routeIs('freelancer.*') || request()->routeIs('company.*');
    $navBaseClass = 'nav-link flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all duration-200';
    $navDefaultClass = 'text-gray-700 hover:bg-gray-100';
    $navActiveClass = 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg';
@endphp
<!-- Header（ページ上部に固定） -->
<header class="fixed top-0 left-0 right-0 z-[5000] bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-5 lg:px-8">
        <div class="grid grid-cols-[auto_1fr_auto] items-center h-16 gap-4">
            <!-- Logo -->
            <a href="{{ route('top') }}" class="flex items-center gap-2 group logo-hover w-fit">
                <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <span class="header-logo-text text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    AIスキルマッチ
                </span>
            </a>

            <!-- Desktop Navigation（中央配置：左右カラムでバランス） -->
            <div class="hidden lg:flex items-center gap-6 justify-self-center">
                <a href="{{ route('questions.index') }}" class="{{ $navBaseClass }} {{ ($isOnRolePage || !request()->routeIs('questions.*')) ? $navDefaultClass : $navActiveClass }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>AI知恵袋</span>
                </a>
                <a href="{{ route('skills.index') }}" class="{{ $navBaseClass }} {{ ($isOnRolePage || !request()->routeIs('skills.*')) ? $navDefaultClass : $navActiveClass }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>スキル販売</span>
                </a>
                <a href="{{ route('profiles.index') }}" class="{{ $navBaseClass }} {{ ($isOnRolePage || !request()->routeIs('profiles.*')) ? $navDefaultClass : $navActiveClass }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>プロフィール</span>
                </a>
                <a href="{{ route('articles.index') }}" class="{{ $navBaseClass }} {{ ($isOnRolePage || !request()->routeIs('articles.*') && !request()->routeIs('my-articles.*')) ? $navDefaultClass : $navActiveClass }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>記事</span>
                </a>
            </div>

            <!-- 右側：ログインボタン（未ログイン時）／各種アイコン（ログイン時）／モバイルメニュー -->
            <div class="flex items-center justify-end gap-3">
                @if(!auth('freelancer')->check() && !auth('company')->check())
                <div class="hidden lg:flex items-center">
                    <a href="{{ route('auth.login.form') }}" class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>ログイン</span>
                    </a>
                </div>
                @elseif(auth('freelancer')->check())
                @php
                    $appUnread = $unreadApplicationCount ?? 0;
                    $scoutUnread = $unreadScoutCount ?? 0;
                    $salesCount = $salesOrderCount ?? 0;
                    $userDisplayName = $freelancer?->display_name ?? 'ゲストユーザー';
                    $userIcon = $freelancer?->icon_path ?? null;
                    $avatarSrc = null;
                    if (!empty($userIcon)) {
                        if (str_starts_with($userIcon, 'http://') || str_starts_with($userIcon, 'https://')) {
                            $avatarSrc = $userIcon;
                        } else {
                            $iconRel = ltrim($userIcon, '/');
                            if (str_starts_with($iconRel, 'storage/')) {
                                $iconRel = substr($iconRel, strlen('storage/'));
                            }
                            $avatarSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($iconRel);
                        }
                    }
                @endphp
                <div class="dropdown relative" id="publicFreelancerUserDropdown">
                    <button class="user-avatar" id="publicFreelancerUserDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="publicFreelancerUserDropdownMenu">
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="プロフィール画像" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            {{ $userInitial ?? 'U' }}
                        @endif
                    </button>
                    <div class="dropdown-content" id="publicFreelancerUserDropdownMenu" role="menu" aria-label="ユーザーメニュー">
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
                            </a>
                            <a href="{{ route('freelancer.applications.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="dropdown-item-text">応募した案件</span>
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
                            <a href="{{ route('direct-messages.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m5 0a2 2 0 01-2 2H6l-3 3V6a2 2 0 012-2h13a2 2 0 012 2v10z"/></svg>
                                <span class="dropdown-item-text">メッセージ</span>
                                @if(($unreadDirectMessageCount ?? 0) > 0)
                                    <span class="dropdown-item-badge dropdown-item-badge-green">新着{{ $unreadDirectMessageCount }}</span>
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
                @elseif(auth('company')->check())
                @php
                    $appUnread = $unreadApplicationCount ?? 0;
                    $scoutUnread = $unreadScoutCount ?? 0;
                    $companyDisplayName = $company?->name ?? 'ゲスト企業';
                @endphp
                <div class="dropdown relative" id="publicCompanyUserDropdown">
                    <button class="user-avatar" id="publicCompanyUserDropdownToggle" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="publicCompanyUserDropdownMenu">
                        {{ $userInitial ?? '企' }}
                    </button>
                    <div class="dropdown-content" id="publicCompanyUserDropdownMenu" role="menu" aria-label="ユーザーメニュー">
                        <div class="dropdown-profile">
                            <div class="dropdown-profile-avatar-initial">{{ $userInitial ?? '企' }}</div>
                            <div class="dropdown-profile-info">
                                <div class="dropdown-profile-name">{{ $companyDisplayName }}</div>
                                <div class="dropdown-profile-role">企業</div>
                            </div>
                        </div>
                        <div class="dropdown-nav">
                            <a href="{{ route('company.freelancers.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span class="dropdown-item-text">フリーランス一覧</span>
                            </a>
                            <a href="{{ route('company.jobs.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span class="dropdown-item-text">案件一覧</span>
                            </a>
                            <a href="{{ route('purchased-skills.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="dropdown-item-text">購入したスキル</span>
                            </a>
                            <a href="{{ route('company.applications.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="dropdown-item-text">応募された案件</span>
                                @if($appUnread > 0)
                                    <span class="dropdown-item-badge dropdown-item-badge-blue">新着{{ $appUnread }}</span>
                                @endif
                            </a>
                            <a href="{{ route('direct-messages.index') }}" class="dropdown-item" role="menuitem">
                                <svg class="dropdown-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m5 0a2 2 0 01-2 2H6l-3 3V6a2 2 0 012-2h13a2 2 0 012 2v10z"/></svg>
                                <span class="dropdown-item-text">メッセージ</span>
                                @if(($unreadDirectMessageCount ?? 0) > 0)
                                    <span class="dropdown-item-badge dropdown-item-badge-green">新着{{ $unreadDirectMessageCount }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-nav">
                            <a href="{{ route('company.profile.settings') }}" class="dropdown-item" role="menuitem">
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
                @endif
                <button id="publicMobileMenuBtn" class="nav-toggle public-mobile-menu-btn p-2 text-gray-700 hover:bg-gray-100 rounded-lg" type="button" aria-label="メニュー">
                <svg id="publicMenuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="publicCloseIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="publicMobileMenu" class="hidden">
            <div class="py-4 border-t border-gray-200 space-y-2">
                <a href="{{ route('questions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ ($isOnRolePage || !request()->routeIs('questions.*')) ? 'text-gray-700 hover:bg-gray-100' : 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white' }}">
                    <span>AI知恵袋</span>
                </a>
                <a href="{{ route('skills.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ ($isOnRolePage || !request()->routeIs('skills.*')) ? 'text-gray-700 hover:bg-gray-100' : 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white' }}">
                    <span>スキル販売</span>
                </a>
                <a href="{{ route('profiles.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ ($isOnRolePage || !request()->routeIs('profiles.*')) ? 'text-gray-700 hover:bg-gray-100' : 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white' }}">
                    <span>プロフィール</span>
                </a>
                <a href="{{ route('articles.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ ($isOnRolePage || !request()->routeIs('articles.*') && !request()->routeIs('my-articles.*')) ? 'text-gray-700 hover:bg-gray-100' : 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white' }}">
                    <span>記事</span>
                </a>
                @if(!auth('freelancer')->check() && !auth('company')->check())
                <div class="pt-4 border-t border-gray-200 space-y-2">
                    <a href="{{ route('auth.login.form') }}" class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-medium">
                        <span>ログイン</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </nav>
</header>

<style>
    /* アニメーション */
    .nav-link {
        transition: all 0.2s ease;
        font-size: 0.95rem;
        padding: 0.55rem 0.85rem;
    }
    @media (min-width: 1280px) {
        .nav-link {
            font-size: 1rem;
            padding: 0.65rem 1rem;
        }
    }
    .logo-hover {
        transition: all 0.3s ease;
    }
    .logo-hover:hover {
        transform: scale(1.02);
    }
    .header-logo-text {
        font-size: 1rem;
    }
    @media (min-width: 480px) {
        .header-logo-text { font-size: 1.1rem; }
    }
    @media (min-width: 640px) {
        .header-logo-text { font-size: 1.15rem; }
    }
    @media (min-width: 768px) {
        .header-logo-text { font-size: 1.2rem; }
    }
    @media (min-width: 1024px) {
        .header-logo-text { font-size: 1.35rem; }
    }
    @media (min-width: 1280px) {
        .header-logo-text { font-size: 1.45rem; }
    }
</style>

<style>
    /* 共通ヘッダー右端：ユーザーアイコン/ドロップダウン（roleに依存せず同じ見栄え） */
    #publicFreelancerUserDropdownToggle.user-avatar,
    #publicCompanyUserDropdownToggle.user-avatar {
        width: 32px;
        height: 32px;
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
        overflow: hidden;
    }
    #publicFreelancerUserDropdownToggle.user-avatar:hover,
    #publicCompanyUserDropdownToggle.user-avatar:hover {
        transform: scale(1.06);
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    }

    @media (min-width: 640px) {
        #publicFreelancerUserDropdownToggle.user-avatar,
        #publicCompanyUserDropdownToggle.user-avatar {
            width: 34px;
            height: 34px;
        }
    }
    @media (min-width: 768px) {
        #publicFreelancerUserDropdownToggle.user-avatar,
        #publicCompanyUserDropdownToggle.user-avatar {
            width: 36px;
            height: 36px;
        }
    }
    @media (min-width: 1024px) {
        #publicFreelancerUserDropdownToggle.user-avatar,
        #publicCompanyUserDropdownToggle.user-avatar {
            width: 40px;
            height: 40px;
        }
    }

    #publicFreelancerUserDropdown .dropdown-content,
    #publicCompanyUserDropdown .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 0.5rem);
        background-color: #ffffff;
        min-width: 300px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 12px;
        z-index: 5001;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    #publicFreelancerUserDropdown.is-open .dropdown-content,
    #publicCompanyUserDropdown.is-open .dropdown-content {
        display: block;
    }

    #publicFreelancerUserDropdown .dropdown-profile,
    #publicCompanyUserDropdown .dropdown-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid #f3f4f6;
    }
    #publicFreelancerUserDropdown .dropdown-profile-avatar,
    #publicCompanyUserDropdown .dropdown-profile-avatar {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    #publicFreelancerUserDropdown .dropdown-profile-avatar-initial,
    #publicCompanyUserDropdown .dropdown-profile-avatar-initial {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        flex-shrink: 0;
    }
    #publicFreelancerUserDropdown .dropdown-profile-info,
    #publicCompanyUserDropdown .dropdown-profile-info { min-width: 0; }
    #publicFreelancerUserDropdown .dropdown-profile-name,
    #publicCompanyUserDropdown .dropdown-profile-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        line-height: 1.3;
    }
    #publicFreelancerUserDropdown .dropdown-profile-role,
    #publicCompanyUserDropdown .dropdown-profile-role {
        font-size: 0.8125rem;
        color: #6b7280;
        margin-top: 0.125rem;
    }

    #publicFreelancerUserDropdown .dropdown-nav,
    #publicCompanyUserDropdown .dropdown-nav { padding: 0.5rem 0; }
    #publicFreelancerUserDropdown .dropdown-item,
    #publicCompanyUserDropdown .dropdown-item {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        text-decoration: none;
        color: #374151;
        transition: all 0.15s ease;
        white-space: nowrap;
        font-size: 0.9375rem;
        font-weight: 500;
    }
    #publicFreelancerUserDropdown .dropdown-item:hover,
    #publicCompanyUserDropdown .dropdown-item:hover {
        background-color: #f9fafb;
        color: #111827;
    }
    #publicFreelancerUserDropdown .dropdown-item-icon,
    #publicCompanyUserDropdown .dropdown-item-icon {
        width: 20px;
        height: 20px;
        min-width: 20px;
        flex-shrink: 0;
        color: #6b7280;
    }
    #publicFreelancerUserDropdown .dropdown-item:hover .dropdown-item-icon,
    #publicCompanyUserDropdown .dropdown-item:hover .dropdown-item-icon { color: #374151; }
    #publicFreelancerUserDropdown .dropdown-item-text,
    #publicCompanyUserDropdown .dropdown-item-text {
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #publicFreelancerUserDropdown .dropdown-item-badge,
    #publicCompanyUserDropdown .dropdown-item-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        flex-shrink: 0;
    }
    #publicFreelancerUserDropdown .dropdown-item-badge-green,
    #publicCompanyUserDropdown .dropdown-item-badge-green { background: #22c55e; color: white; }
    #publicFreelancerUserDropdown .dropdown-item-badge-blue,
    #publicCompanyUserDropdown .dropdown-item-badge-blue { background: #3b82f6; color: white; }
    #publicFreelancerUserDropdown .dropdown-item-badge-purple,
    #publicCompanyUserDropdown .dropdown-item-badge-purple { background: #8b5cf6; color: white; }

    #publicFreelancerUserDropdown .dropdown-divider,
    #publicCompanyUserDropdown .dropdown-divider {
        height: 1px;
        background-color: #e5e7eb;
        margin: 0.5rem 1rem;
    }
    #publicFreelancerUserDropdown .dropdown-item-form,
    #publicCompanyUserDropdown .dropdown-item-form { display: block; }
    #publicFreelancerUserDropdown .dropdown-item-logout,
    #publicCompanyUserDropdown .dropdown-item-logout {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        font-size: inherit;
        font-family: inherit;
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.75rem;
        white-space: nowrap;
        color: #dc2626 !important;
    }
    #publicFreelancerUserDropdown .dropdown-item-logout:hover,
    #publicCompanyUserDropdown .dropdown-item-logout:hover {
        background-color: #fef2f2 !important;
        color: #b91c1c !important;
    }
    #publicFreelancerUserDropdown .dropdown-item-logout .dropdown-item-icon,
    #publicCompanyUserDropdown .dropdown-item-logout .dropdown-item-icon { color: #dc2626; }

    #publicCompanyUserDropdown .dropdown-item-disabled {
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.85;
    }
    #publicCompanyUserDropdown .dropdown-item-disabled:hover { background-color: transparent; color: #9ca3af; }
    #publicCompanyUserDropdown .dropdown-item-disabled .dropdown-item-icon { color: #9ca3af; }

    #publicMobileMenu {
        z-index: 5001;
    }

    /* 早めにハンバーガーへ切り替える */
    .nav-toggle {
        display: inline-flex;
    }
    @media (min-width: 1024px) {
        .nav-toggle {
            display: none;
        }
        .nav-links {
            display: flex !important;
        }
    }
    .nav-links {
        display: none;
    }
    @media (min-width: 1280px) {
        .nav-links {
            gap: 3rem;
        }
        .header-logo-text {
            font-size: 1.45rem;
        }
    }
</style>

<script>
    (function () {
        const btn = document.getElementById('publicMobileMenuBtn');
        const menu = document.getElementById('publicMobileMenu');
        const menuIcon = document.getElementById('publicMenuIcon');
        const closeIcon = document.getElementById('publicCloseIcon');

        if (btn && menu && menuIcon && closeIcon) {
            btn.addEventListener('click', () => {
                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    menuIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                } else {
                    menu.classList.add('hidden');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            });
        }

        const setupDropdown = (dropdownId, toggleId) => {
            const dropdown = document.getElementById(dropdownId);
            const dropdownToggle = document.getElementById(toggleId);
            if (!dropdown || !dropdownToggle) return;

            const close = () => { dropdown.classList.remove('is-open'); dropdownToggle.setAttribute('aria-expanded', 'false'); };
            const open = () => { dropdown.classList.add('is-open'); dropdownToggle.setAttribute('aria-expanded', 'true'); };
            const isOpen = () => dropdown.classList.contains('is-open');

            dropdownToggle.addEventListener('click', (e) => { e.stopPropagation(); isOpen() ? close() : open(); });
            document.addEventListener('click', (e) => { if (!dropdown.contains(e.target)) close(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        };

        // 共通ヘッダー内のユーザーアイコン用ドロップダウン
        setupDropdown('publicFreelancerUserDropdown', 'publicFreelancerUserDropdownToggle');
        setupDropdown('publicCompanyUserDropdown', 'publicCompanyUserDropdownToggle');
    })();
</script>

{{-- ログイン後ヘッダー（フリーランス/企業）を public ヘッダー直下に固定 --}}
<style>
    .header-role {
        position: fixed !important;
        top: 4rem !important;
        left: 0;
        right: 0;
        z-index: 40;
    }
</style>
