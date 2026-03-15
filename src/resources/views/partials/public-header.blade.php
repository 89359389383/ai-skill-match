@php
    $isOnRolePage = request()->routeIs('freelancer.*') || request()->routeIs('company.*');
    $navBaseClass = 'nav-link flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all duration-200';
    $navDefaultClass = 'text-gray-700 hover:bg-gray-100';
    $navActiveClass = 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg';
@endphp
<!-- Header（ページ上部に固定） -->
<header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-[1fr_auto_1fr] items-center h-16 gap-4">
            <!-- Logo -->
            <a href="{{ route('top') }}" class="flex items-center gap-2 group logo-hover w-fit">
                <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    AIスキルマッチ
                </span>
            </a>

            <!-- Desktop Navigation（中央配置：左右カラムでバランス） -->
            <div class="hidden md:flex items-center gap-6 justify-self-center">
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

            <!-- 右側：ログインボタン（未ログイン時）またはモバイルメニュー -->
            <div class="flex items-center justify-end gap-3">
                @if(!auth('freelancer')->check() && !auth('company')->check())
                <div class="hidden md:flex items-center">
                    <a href="{{ route('auth.login.form') }}" class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-medium hover:shadow-lg transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>ログイン</span>
                    </a>
                </div>
                @endif
                <button id="publicMobileMenuBtn" class="md:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-lg" type="button" aria-label="メニュー">
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
        <div id="publicMobileMenu" class="md:hidden hidden">
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
    }
    .logo-hover {
        transition: all 0.3s ease;
    }
    .logo-hover:hover {
        transform: scale(1.02);
    }
</style>

<script>
    (function () {
        const btn = document.getElementById('publicMobileMenuBtn');
        const menu = document.getElementById('publicMobileMenu');
        const menuIcon = document.getElementById('publicMenuIcon');
        const closeIcon = document.getElementById('publicCloseIcon');

        if (!btn || !menu || !menuIcon || !closeIcon) return;

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
