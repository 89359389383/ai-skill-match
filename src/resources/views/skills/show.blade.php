@extends('layouts.public')

@section('title', ($listing->title ?? 'スキル詳細') . ' - AIスキルマッチ')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm text-gray-500">
            <a href="{{ route('top') }}" class="hover:text-gray-900">ホーム</a>
            <span class="mx-2">></span>
            <a href="{{ route('skills.index') }}" class="hover:text-gray-900">スキル販売</a>
            <span class="mx-2">></span>
            <span class="font-bold text-gray-900">{{ Str::limit($listing->title, 40) }}</span>
        </nav>

        @include('partials.error-panel')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl mb-6">
                    <img src="{{ $listing->thumbnail_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $listing->title }}" class="w-full h-96 object-cover">
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $listing->title }}</h1>

                    @php
                        // 出品者アイコン表示用（null/ローカルパス/storage対応）
                        $seller = $listing->freelancer;
                        $sellerIconPath = $seller?->icon_path ?? null;
                        $sellerDefaultIcon = 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop';
                        $sellerAvatarSrc = $sellerDefaultIcon;

                        if (!empty($sellerIconPath)) {
                            if (str_starts_with($sellerIconPath, 'http://') || str_starts_with($sellerIconPath, 'https://')) {
                                $sellerAvatarSrc = $sellerIconPath;
                            } else {
                                $iconRel = ltrim((string) $sellerIconPath, '/');
                                if (str_starts_with($iconRel, 'storage/')) {
                                    $iconRel = substr($iconRel, strlen('storage/'));
                                }
                                $sellerAvatarSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($iconRel);
                            }
                        }
                    @endphp

                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center gap-3">
                            <a
                                href="{{ route('profiles.show', ['user' => $seller->user_id]) }}"
                                class="shrink-0"
                                aria-label="出品者プロフィールへ"
                            >
                                <img
                                    src="{{ $sellerAvatarSrc }}"
                                    alt="{{ $seller->display_name ?? '出品者' }}"
                                    class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm"
                                >
                            </a>
                            <div class="flex flex-col">
                                <a
                                    href="{{ route('profiles.show', ['user' => $seller->user_id]) }}"
                                    class="font-bold text-gray-900 text-sm hover:underline"
                                >
                                    {{ $seller->display_name ?? '出品者' }}
                                </a>
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-yellow-400">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    <span class="font-bold text-gray-900">{{ $listing->rating_average ?? '0' }}</span>
                                    <span class="text-sm text-gray-500">({{ $listing->reviews_count ?? 0 }}件のレビュー)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($listing->skills as $skill)
                            <span class="px-3 py-1 bg-purple-50 text-purple-600 text-sm rounded-full">{{ $skill->name }}</span>
                        @endforeach
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">サービス内容</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $listing->description }}</p>

                    <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">購入にあたって</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $listing->purchase_instructions }}</p>
                </div>

                {{-- 販売者情報セクション（不要なため非表示） --}}
                {{-- <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">販売者情報</h2>
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-6">
                        <div class="bg-white rounded-xl p-6 relative">
                            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                                <img src="{{ $sellerAvatarSrc }}" alt="{{ $seller->display_name }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                            </div>
                            <div class="mt-16 text-center">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $seller->display_name ?? '出品者' }}</h3>
                                <p class="text-gray-600 mb-6">{{ $seller->job_title ?? '-' }}</p>
                                <a href="{{ route('profiles.show', ['user' => $seller->user_id]) }}" class="inline-block w-full px-6 py-3 border-2 border-purple-600 text-purple-600 rounded-xl font-semibold hover:bg-purple-50 transition-all">
                                    プロフィールを見る
                                </a>
                            </div>
                        </div>
                    </div>
                </div> --}}

                @if($listing->reviews->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">レビュー</h2>
                    <div class="space-y-6">
                        @foreach($listing->reviews->take(5) as $review)
                            <div class="border-b border-gray-200 pb-6 last:border-0">
                                <div class="flex items-start gap-4">
                                    @php
                                        $reviewerF = $review->user?->freelancer;
                                        $reviewerC = $review->user?->company;
                                        $isCompanyReviewer = $reviewerC !== null;

                                        $reviewName = $isCompanyReviewer
                                            ? ($reviewerC->contact_name ?? $reviewerC->name ?? $review->user?->email ?? '匿名')
                                            : ($reviewerF?->display_name ?? $review->user?->email ?? '匿名');

                                        $reviewIconPath = $isCompanyReviewer
                                            ? ($reviewerC?->icon_path ?? null)
                                            : ($reviewerF?->icon_path ?? null);
                                        $reviewDefaultIcon = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop';
                                        $reviewAvatarSrc = $reviewDefaultIcon;

                                        if (!empty($reviewIconPath)) {
                                            if (str_starts_with($reviewIconPath, 'http://') || str_starts_with($reviewIconPath, 'https://')) {
                                                $reviewAvatarSrc = $reviewIconPath;
                                            } else {
                                                $reviewIconRel = ltrim((string) $reviewIconPath, '/');
                                                if (str_starts_with($reviewIconRel, 'storage/')) {
                                                    $reviewIconRel = substr($reviewIconRel, strlen('storage/'));
                                                }
                                                $reviewAvatarSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($reviewIconRel);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $reviewAvatarSrc }}" alt="{{ $reviewName }}" class="w-12 h-12 rounded-full object-cover">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-bold text-gray-900">{{ $reviewName }}</div>
                                            <div class="text-sm text-gray-500">{{ $review->created_at?->format('Y/m/d') }}</div>
                                        </div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="flex items-center gap-0.5" aria-label="評価 {{ $review->rating ?? 0 }}点">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="{{ ($review->rating ?? 0) >= $i ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" class="w-4 h-4 {{ ($review->rating ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-300' }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                                @endfor
                                            </div>
                                            <span class="font-bold text-gray-900">{{ $review->rating ?? 0 }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{{ $review->body ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl p-6 mb-6">
                            <div class="text-3xl font-bold mb-2">¥{{ number_format($listing->price) }}</div>
                            @if($listing->delivery_days)
                                <div class="text-left align-bottom font-bold text-[22px]">納期: {{ $listing->delivery_days }}日</div>
                            @endif
                        </div>

                        @php
                            $sellerUserId = $listing->freelancer?->user_id;
                        @endphp

                        <a href="{{ route('profiles.skills.index', ['user' => $sellerUserId]) }}"
                           class="inline-flex items-center gap-2 mb-4 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                            スキル一覧
                        </a>

                        @php
                            $isOwnListing = auth('freelancer')->check()
                                && auth('freelancer')->user()->freelancer
                                && (int) auth('freelancer')->user()->freelancer->id === (int) $listing->freelancer_id;
                        @endphp

                        @php
                            $isGuest = !auth()->check() && !auth('freelancer')->check() && !auth('company')->check();
                            $currentUserId = auth('company')->check()
                                ? auth('company')->user()->id
                                : (auth('freelancer')->check() ? auth('freelancer')->user()->id : null);
                            $isOwnProfile = $currentUserId && (int) $currentUserId === (int) $sellerUserId;
                            $canStartDirectMessage = !$isGuest && !$isOwnProfile;
                            $loginRedirectUrl = route('auth.login.form', [
                                'redirect' => route('skills.show', [
                                    'skill_listing' => $listing->id,
                                    'open_message_modal' => 1,
                                ]),
                            ]);
                            $defaultMessageContent = '(' . ($listing->title ?? 'スキル') . ')について相談させてください。';
                        @endphp

                        @if (!$isOwnListing)
                            <form action="{{ route('skills.purchase', ['skill_listing' => $listing->id]) }}" method="POST" class="mb-4">
                                @csrf
                                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                                    購入する
                                </button>
                            </form>

                            @if($isGuest)
                                <a href="{{ $loginRedirectUrl }}" class="w-full px-6 py-3 bg-purple-50 border-2 border-purple-200 text-purple-700 rounded-xl font-semibold hover:bg-purple-100 transition-all inline-flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    ログインして問い合わせる
                                </a>
                            @elseif($canStartDirectMessage)
                                <button
                                    type="button"
                                    onclick="openDirectMessageModal()"
                                    class="w-full px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all inline-flex items-center justify-center gap-2"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    問い合わせる
                                </button>
                            @endif
                        @else
                            <p class="text-center text-gray-500 font-medium py-4">あなたの出品です</p>
                            <div class="flex flex-col gap-3">
                                <a href="{{ route('skills.edit', array_merge(['skill_listing' => $listing->id], request()->filled('slot') ? ['slot' => request('slot')] : [])) }}"
                                   class="w-full px-6 py-3 bg-gray-100 border border-gray-200 text-gray-800 rounded-xl font-semibold hover:bg-gray-200 transition-all inline-flex items-center justify-center gap-2"
                                   onclick="event.stopPropagation();">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                    </svg>
                                    編集
                                </a>

                                <button type="button"
                                        class="w-full px-6 py-3 border-2 border-red-200 text-red-700 rounded-xl font-semibold hover:bg-red-50 transition-all inline-flex items-center justify-center gap-2"
                                        onclick="openSkillDeleteModal('{{ route('skills.destroy', array_merge(['skill_listing' => $listing->id], request()->filled('slot') ? ['slot' => request('slot')] : [])) }}');">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                    削除
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($canStartDirectMessage)
        <div id="directMessageModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 px-4">
            <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">メッセージを送る</h3>
                        <p class="text-sm text-gray-500">{{ $listing->freelancer?->display_name ?? '相手' }}さんへ最初のメッセージを送信します。</p>
                    </div>
                    <button type="button" onclick="closeDirectMessageModal()" class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('direct-messages.start', ['user' => $sellerUserId]) }}" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label for="directMessageContent" class="mb-2 block text-sm font-semibold text-gray-700">メッセージ本文</label>
                        <textarea
                            id="directMessageContent"
                            name="content"
                            rows="6"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('content') border-red-500 ring-2 ring-red-100 @enderror"
                            placeholder="最初のメッセージを入力してください"
                        >{{ old('content') ?: $defaultMessageContent }}</textarea>
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

    <div id="skillDeleteModal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">スキルを削除しますか？</h3>
            <p id="skillDeleteModalMessage" class="text-sm text-gray-600 mb-6">この操作は取り消せません。</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSkillDeleteModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">キャンセル</button>
                <form id="skillDeleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700">削除する</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentSkillDeleteUrl = null;

        function openSkillDeleteModal(destroyUrl) {
            currentSkillDeleteUrl = destroyUrl;
            const modal = document.getElementById('skillDeleteModal');
            const form = document.getElementById('skillDeleteForm');
            if (!modal || !form) return;
            form.action = currentSkillDeleteUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeSkillDeleteModal() {
            const modal = document.getElementById('skillDeleteModal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            currentSkillDeleteUrl = null;
        }

        document.getElementById('skillDeleteModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeSkillDeleteModal();
        });

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
</div>
@endsection
