@extends('layouts.public')

@section('title', ($freelancer->display_name ?? 'プロフィール') . ' - AIスキルマッチ')

@push('styles')
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('profiles.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            プロフィール一覧に戻る
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-24">
                    <!-- Sidebar Header -->
                    <div class="bg-orange-500 text-white px-6 py-4">
                        <h2 class="font-bold text-center">
                            {{ $freelancer->display_name ?? '名前未設定' }}さんの<br>詳細プロフィール
                        </h2>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="border-b">
                        <button onclick="scrollToSection('profile')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">
                            プロフィール
                        </button>
                        <button onclick="scrollToSection('experience')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">
                            職歴・経歴
                        </button>
                        <button onclick="scrollToSection('services')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">
                            提供サービス
                        </button>
                        <button onclick="scrollToSection('skills')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">
                            スキル
                        </button>
                        <button onclick="scrollToSection('certifications')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">
                            資格
                        </button>
                        <button onclick="scrollToSection('contact')" class="w-full text-left px-6 py-3 text-sm border-b transition-colors text-gray-700 hover:bg-gray-50">
                            連絡先
                        </button>
                        <button onclick="scrollToSection('blog')" class="w-full text-left px-6 py-3 text-sm transition-colors text-gray-700 hover:bg-gray-50">
                            ブログ
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                @php
                    $allSkills = $freelancer->skills->pluck('name')->merge($freelancer->customSkills->pluck('name'))->values();
                    $skillCount = $allSkills->count();
                    $initialSkills = $allSkills->take(3);
                    $additionalSkills = $allSkills->slice(3);
                @endphp

                <!-- Header with Name and Photo -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="profile">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                                {{ $freelancer->display_name ?? '名前未設定' }}
                            </h1>
                            <p class="text-lg text-gray-700 mb-6">
                                {{ $freelancer->bio ? Str::limit(strip_tags($freelancer->bio), 80) : 'プロフィールを登録しています' }}
                            </p>

                            <!-- Info Grid -->
                            <div class="space-y-3">
                                <div class="flex items-start gap-3">
                                    <span class="text-sm font-bold text-orange-600 min-w-[100px]">対応業務</span>
                                    <div class="flex-1">
                                        <span class="text-sm text-gray-700">{{ $freelancer->services_offered ?? ($freelancer->work_style_text ? Str::limit($freelancer->work_style_text, 100) : '—') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <span class="text-sm font-bold text-orange-600 min-w-[100px]">職種</span>
                                    <span class="text-sm text-blue-600">{{ $freelancer->job_title ?? '—' }}</span>
                                </div>

                                <div class="flex items-start gap-3">
                                    <span class="text-sm font-bold text-orange-600 min-w-[100px]">希望時給単価</span>
                                    <span class="text-sm text-gray-700">¥{{ number_format($freelancer->min_rate ?? 0) }}〜@if($freelancer->max_rate)¥{{ number_format($freelancer->max_rate) }}@endif</span>
                                </div>

                                <div class="flex items-start gap-3">
                                    <span class="text-sm font-bold text-orange-600 min-w-[100px]">スキル</span>
                                    <div class="flex-1 flex flex-wrap gap-2">
                                        @foreach($initialSkills as $skill)
                                            <span class="px-3 py-1 bg-orange-500 text-white text-sm rounded-full">{{ $skill }}</span>
                                        @endforeach
                                        @if($additionalSkills->isNotEmpty())
                                            <button class="text-sm text-blue-600 hover:underline" onclick="toggleSkills()">
                                                <span id="skillsToggleText">もっと見る（登録スキル数：{{ $skillCount }}）</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @if($additionalSkills->isNotEmpty())
                                <div id="additionalSkills" style="display: none;" class="flex items-start gap-3">
                                    <span class="text-sm font-bold text-orange-600 min-w-[100px]"></span>
                                    <div class="flex-1 flex flex-wrap gap-2">
                                        @foreach($additionalSkills as $skill)
                                            <span class="px-3 py-1 bg-orange-500 text-white text-sm rounded-full">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div class="flex items-start gap-3">
                                    <span class="text-sm font-bold text-orange-600 min-w-[100px]">得意業種</span>
                                    <div class="flex-1 flex flex-wrap gap-2">
                                        @if($freelancer->industry_specialties)
                                            @foreach(array_map('trim', explode(',', $freelancer->industry_specialties)) as $industry)
                                                @if($industry)
                                                <span class="px-3 py-1 bg-orange-500 text-white text-sm rounded-full">{{ $industry }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-sm text-gray-500">—</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <span class="text-sm font-bold text-orange-600 min-w-[100px]">在住都道府県</span>
                                    <span class="text-sm text-blue-600">{{ $freelancer->prefecture ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Image -->
                        <div class="ml-6 flex-shrink-0">
                            @php
                                $iconPath = $freelancer->icon_path ?? null;
                                $iconSrc = null;
                                if (!empty($iconPath)) {
                                    if (str_starts_with($iconPath, 'http://') || str_starts_with($iconPath, 'https://')) {
                                        $iconSrc = $iconPath;
                                    } else {
                                        $iconRel = ltrim($iconPath, '/');
                                        if (str_starts_with($iconRel, 'storage/')) {
                                            $iconRel = substr($iconRel, strlen('storage/'));
                                        }
                                        $iconSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($iconRel);
                                    }
                                }
                            @endphp
                            <img src="{{ $iconSrc ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=160&h=160&fit=crop' }}" alt="{{ $freelancer->display_name }}" class="w-40 h-40 object-cover rounded-lg shadow-md">
                        </div>
                    </div>
                </div>

                <!-- Detailed Profile Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="experience">
                    <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">
                        詳細プロフィール
                    </h2>
                    <div class="text-gray-700 whitespace-pre-wrap leading-relaxed">
                        {{ $freelancer->bio ?? '自己紹介はまだありません。' }}
                    </div>
                </div>

                <!-- Responsibilities Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">
                        担当業務・得意業務
                    </h2>
                    <div class="text-gray-700 whitespace-pre-wrap leading-relaxed">
                        {{ $freelancer->work_style_text ?? $freelancer->experience_companies ?? '担当業務の情報はまだありません。' }}
                    </div>
                </div>

                <!-- Services Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="services">
                    <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">
                        提供サービス
                    </h2>
                    @php $skillListings = $freelancer->skillListings; @endphp
                    @if($skillListings->isEmpty())
                        <p class="text-gray-500">まだサービスを出品していません</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($skillListings as $sl)
                            <a href="{{ route('skills.show', ['skill_listing' => $sl->id]) }}" class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all">
                                <div class="aspect-video w-full overflow-hidden">
                                    <img src="{{ $sl->thumbnail_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400&h=225&fit=crop' }}" alt="{{ $sl->title }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 hover:text-orange-600 transition-colors">
                                        {{ $sl->title }}
                                    </h3>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <span class="font-bold text-gray-900">{{ $sl->rating_average ?? '0' }}</span>
                                            <span class="text-sm text-gray-500">({{ $sl->reviews_count ?? 0 }})</span>
                                        </div>
                                        <div class="font-bold text-orange-600">
                                            ¥{{ number_format($sl->price) }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Skills Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="skills">
                    <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">
                        スキル
                    </h2>
                    @if($allSkills->isEmpty())
                        <p class="text-gray-500">まだスキルを登録していません</p>
                    @else
                        <div class="flex flex-wrap gap-3">
                            @foreach($allSkills as $skill)
                                <span class="px-4 py-2 bg-orange-500 text-white text-sm rounded-full">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Certifications Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="certifications">
                    <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">
                        資格
                    </h2>
                    @if($freelancer->certifications)
                        <ul class="text-gray-700 space-y-2">
                            @foreach(array_filter(explode("\n", $freelancer->certifications)) as $line)
                                <li>{{ trim($line) }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">資格情報はまだありません</p>
                    @endif
                </div>

                <!-- Contact Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="contact">
                    <h2 class="text-xl font-bold text-orange-600 mb-4 pb-2 border-b-2 border-orange-600">
                        連絡先
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-sm font-bold">電話番号</span>
                            </div>
                            <span class="text-sm {{ $freelancer->phone ? 'text-gray-700' : 'text-gray-500' }}">{{ $freelancer->phone ?: '未登録' }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-bold">メールアドレス</span>
                            </div>
                            <span class="text-sm {{ $user->email ? 'text-gray-700' : 'text-gray-500' }}">{{ $user->email ?: '未登録' }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="text-sm font-bold">LINE</span>
                            </div>
                            @if($freelancer->line_id)
                                @if(str_starts_with($freelancer->line_id, 'http'))
                                    <a href="{{ $freelancer->line_id }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:underline">{{ $freelancer->line_id }}</a>
                                @else
                                    <span class="text-sm text-gray-700">{{ $freelancer->line_id }}</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500">未登録</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 text-orange-600">
                                <span class="text-sm font-bold">Twitter</span>
                            </div>
                            @if($freelancer->twitter_url)
                                <a href="{{ $freelancer->twitter_url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:underline">{{ $freelancer->twitter_url }}</a>
                            @else
                                <span class="text-sm text-gray-500">未登録</span>
                            @endif
                        </div>
                        @php
                            $isGuest = !auth()->check() && !auth('freelancer')->check() && !auth('company')->check();
                            $currentUser = auth('company')->check() ? auth('company')->user() : (auth('freelancer')->check() ? auth('freelancer')->user() : null);
                            $currentUserId = $currentUser?->id;
                            $isOwnProfile = $currentUserId && $currentUserId === $user->id;
                            // フリーランスまたは企業としてログインしていれば、自分以外のプロフィールにメッセージ送信可能
                            $canStartDirectMessage = !$isGuest && !$isOwnProfile;
                            $loginRedirectUrl = route('auth.login.form', [
                                'redirect' => route('profiles.show', ['user' => $user->id, 'open_message_modal' => 1]),
                            ]);
                        @endphp
                        @if($isGuest && !$isOwnProfile)
                            <p class="text-sm text-gray-500">連絡するにはログインが必要です。</p>
                            <a href="{{ $loginRedirectUrl }}" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                ログインして連絡する
                            </a>
                        @elseif($canStartDirectMessage)
                            <p class="text-sm text-gray-500">メッセージを送信できます。</p>
                            <button type="button" onclick="openDirectMessageModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m5 0a2 2 0 01-2 2H6l-3 3V6a2 2 0 012-2h13a2 2 0 012 2v10z"/>
                                </svg>
                                メッセージを送る
                            </button>
                        @endif
                    </div>
                </div>

                @if($canStartDirectMessage)
                    <div id="directMessageModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4">
                        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
                            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">メッセージを送る</h3>
                                    <p class="text-sm text-gray-500">{{ $freelancer->display_name ?? '相手' }}さんへ最初のメッセージを送信します。</p>
                                </div>
                                <button type="button" onclick="closeDirectMessageModal()" class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('direct-messages.start', ['user' => $user->id]) }}" class="px-6 py-5 space-y-4">
                                @csrf
                                <div>
                                    <label for="directMessageContent" class="mb-2 block text-sm font-semibold text-gray-700">メッセージ本文</label>
                                    <textarea
                                        id="directMessageContent"
                                        name="content"
                                        rows="6"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('content') border-red-500 ring-2 ring-red-100 @enderror"
                                        placeholder="最初のメッセージを入力してください"
                                    >{{ old('content') }}</textarea>
                                    @error('content')
                                        <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center justify-end gap-3">
                                    <button type="button" onclick="closeDirectMessageModal()" class="rounded-xl border border-gray-300 px-5 py-3 font-semibold text-gray-700 hover:bg-gray-50">
                                        キャンセル
                                    </button>
                                    <button type="submit" class="rounded-xl bg-orange-500 px-5 py-3 font-semibold text-white hover:bg-orange-600">
                                        送信してチャットを開始
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Blog/Articles Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6" id="blog">
                    @php
                        $profileViewerNav = auth('freelancer')->user() ?? auth('company')->user();
                        $isOwnProfileNav = $profileViewerNav && (int) $profileViewerNav->id === (int) $user->id;
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-2 border-b-2 border-orange-600">
                        <h2 class="text-xl font-bold text-orange-600">
                            ブログ
                        </h2>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('articles.index', ['user' => $user->id]) }}" class="text-sm font-semibold text-orange-600 hover:text-orange-800 hover:underline">
                                このユーザーの記事一覧へ
                            </a>
                            @if($isOwnProfileNav)
                                <span class="text-gray-300 hidden sm:inline">|</span>
                                <a href="{{ route('my-articles.index') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-800 hover:underline">
                                    自分の記事を管理
                                </a>
                            @endif
                        </div>
                    </div>
                    @if(isset($articles) && $articles->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($articles as $article)
                            <a href="{{ route('articles.show', ['article' => $article->id]) }}" class="flex gap-4 p-4 border border-gray-200 rounded-lg hover:shadow-lg transition-all">
                                <div class="w-32 h-24 flex-shrink-0 rounded-lg overflow-hidden">
                                    <img src="{{ $article->eyecatch_image_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=128&h=96&fit=crop' }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded">
                                            {{ $article->category ?? 'その他' }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-1 line-clamp-2 hover:text-orange-600 transition-colors">
                                        {{ $article->title }}
                                    </h3>
                                    <div class="flex items-center gap-3 text-sm text-gray-500">
                                        <span>{{ number_format($article->views_count ?? 0) }} views</span>
                                        <span>{{ number_format($article->likes_count ?? 0) }} likes</span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">まだ記事を投稿していません</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let showAllSkills = false;

    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function toggleSkills() {
        showAllSkills = !showAllSkills;
        const additionalSkills = document.getElementById('additionalSkills');
        const toggleText = document.getElementById('skillsToggleText');
        if (!additionalSkills || !toggleText) return;

        if (showAllSkills) {
            additionalSkills.style.display = 'flex';
            toggleText.textContent = '閉じる';
        } else {
            additionalSkills.style.display = 'none';
            toggleText.textContent = 'もっと見る（登録スキル数：{{ $skillCount }}）';
        }
    }

    (function () {
        const modal = document.getElementById('directMessageModal');
        if (!modal) return;

        const shouldOpen = @json((bool) request()->boolean('open_message_modal') || $errors->has('content') || old('content'));

        window.openDirectMessageModal = function () {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        window.closeDirectMessageModal = function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                window.closeDirectMessageModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                window.closeDirectMessageModal();
            }
        });

        if (shouldOpen) {
            window.openDirectMessageModal();
        }
    })();
</script>
@endsection
