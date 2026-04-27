@extends('layouts.public')

@section('title', 'スキル販売 - AITECH Pro Match')

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
<style>
.profiles-pagination-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
}

.profiles-pagination-bar a,
.profiles-pagination-bar span.profiles-page-ellipsis {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    padding: 0 0.35rem;
    border-radius: 0.35rem;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1;
}

.profiles-pagination-bar a.profiles-page-link {
    background-color: #f3f4f6;
    color: #111827;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.profiles-pagination-bar a.profiles-page-link:hover:not(.profiles-page-active) {
    background-color: #e5e7eb;
}

.profiles-pagination-bar a.profiles-page-active {
    background-color: #FC4C0C;
    color: #fff;
    pointer-events: none;
    cursor: default;
}

.profiles-pagination-bar span.profiles-page-ellipsis {
    min-width: 1.75rem;
    color: #6b7280;
    font-weight: 600;
    user-select: none;
}

.profiles-pagination-bar a.profiles-page-nav {
    background-color: #f3f4f6;
    color: #92400e;
}

.profiles-pagination-bar a.profiles-page-nav:hover:not(.profiles-page-nav-disabled) {
    background-color: #e5e7eb;
}

.profiles-pagination-bar span.profiles-page-nav-disabled {
    background-color: #f3f4f6;
    color: #d1d5db;
    cursor: not-allowed;
    user-select: none;
}
</style>
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    @php
                        $viewerFreelancer = auth('freelancer')->check() ? auth('freelancer')->user()->freelancer : null;
                        $currentVisibility = $visibility ?? request()->query('visibility', 'public');
                        $isOwnSkillList = isset($freelancer) && $freelancer
                            && $viewerFreelancer
                            && (int) $viewerFreelancer->id === (int) $freelancer->id;
                    @endphp
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        {{ isset($freelancer) && $freelancer ? ($freelancer->display_name . 'さんのスキル一覧') : 'スキル販売' }}
                    </h1>

                    @if(isset($freelancer) && $freelancer && !$isOwnSkillList && $currentVisibility === 'public')
                        <p class="text-sm text-indigo-600 mt-2">
                            特定ユーザーの公開スキルのみ表示しています。
                            <a href="{{ route('skills.index', request()->filled('slot') ? ['slot' => request('slot')] : []) }}" class="underline font-medium">
                                すべてのスキルを見る
                            </a>
                        </p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if($isOwnSkillList)
                        <div class="flex items-center gap-2">
                            <a href="{{ route('profiles.skills.index', array_merge(['user' => auth('freelancer')->user()], request()->filled('slot') ? ['slot' => request('slot')] : [], ['visibility' => 'all'])) }}"
                               class="px-4 py-3 rounded-xl font-bold text-lg {{ $currentVisibility === 'all' ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                                全て
                            </a>
                            <a href="{{ route('profiles.skills.index', array_merge(['user' => auth('freelancer')->user()], request()->filled('slot') ? ['slot' => request('slot')] : [], ['visibility' => 'public'])) }}"
                               class="px-4 py-3 rounded-xl font-bold text-lg {{ $currentVisibility === 'public' ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                                公開
                            </a>
                            <a href="{{ route('profiles.skills.index', array_merge(['user' => auth('freelancer')->user()], request()->filled('slot') ? ['slot' => request('slot')] : [], ['visibility' => 'private'])) }}"
                               class="px-4 py-3 rounded-xl font-bold text-lg {{ $currentVisibility === 'private' ? 'bg-indigo-700 text-white' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                                非公開
                            </a>
                        </div>
                    @endif
                    @if($isOwnSkillList)
                        <a href="{{ route('profiles.skills.index', array_merge(['user' => auth('freelancer')->user()], request()->filled('slot') ? ['slot' => request('slot')] : [], ['visibility' => $currentVisibility])) }}"
                           class="flex items-center gap-2 px-6 py-4 border-2 border-indigo-200 text-indigo-700 rounded-xl font-bold shadow-sm hover:bg-indigo-50 transition-all duration-300 text-lg">
                            自分のスキル一覧
                        </a>
                    @endif
                    @if(auth('freelancer')->check())
                        <a href="{{ route('skills.create', request()->filled('slot') ? ['slot' => request('slot')] : []) }}"
                           class="flex items-center gap-2 px-6 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold shadow-sm hover:shadow-md transform hover:-translate-y-1 transition-all duration-300 text-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            スキルを出品
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 font-bold">
                {{ session('success') }}
            </div>
        @endif

        @php
            $keywordValue = request()->query('keyword', '');
            $priceMinValue = request()->query('price_min', '');
            $priceMaxValue = request()->query('price_max', '');
            // クリアボタンで除外するパラメータ
            $clearQuery = request()->except(['keyword', 'price_min', 'price_max', 'page']);
            $clearUrl = url()->current();
            if (!empty($clearQuery)) {
                $clearUrl .= '?' . http_build_query($clearQuery);
            }
            // スクリーンショットに近い選択肢（必要なら調整）
            $priceOptions = [
                '' => '指定なし',
                500 => '500円',
                1000 => '1,000円',
                1500 => '1,500円',
                2000 => '2,000円',
                2500 => '2,500円',
                3000 => '3,000円',
                3500 => '3,500円',
                4000 => '4,000円',
                4500 => '4,500円',
                5000 => '5,000円',
                6000 => '6,000円',
                7000 => '7,000円',
                8000 => '8,000円',
                9000 => '9,000円',
                10000 => '10,000円',
                15000 => '15,000円',
                20000 => '20,000円',
                30000 => '30,000円',
                40000 => '40,000円',
                50000 => '50,000円',
                60000 => '60,000円',
                70000 => '70,000円',
                80000 => '80,000円',
                90000 => '90,000円',
                100000 => '100,000円',
                150000 => '150,000円',
                200000 => '200,000円',
                300000 => '300,000円',
                400000 => '400,000円',
                500000 => '500,000円',
            ];
        @endphp

        {{-- 検索フィルター --}}
        <div class="flex justify-center mb-6">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-3 justify-center">
                {{-- 既存のクエリ（visibility / slot など）を保持 --}}
                @foreach(request()->except(['keyword', 'price_min', 'price_max', 'page']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                {{-- タイトル/サービス内容 --}}
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タイトル・サービス内容</label>
                    <input
                        type="text"
                        name="keyword"
                        value="{{ $keywordValue }}"
                        placeholder="例：業務効率化 / 自動化"
                        class="w-80 max-w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    >
                </div>

                {{-- 予算（価格） --}}
                <div class="w-full sm:w-auto">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">予算（価格）</label>
                    <div class="flex items-center gap-2">
                        <select
                            name="price_min"
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        >
                            @foreach($priceOptions as $value => $label)
                                <option value="{{ $value }}" {{ (string) $priceMinValue === (string) $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-gray-500 font-bold">〜</span>
                        <select
                            name="price_max"
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        >
                            @foreach($priceOptions as $value => $label)
                                <option value="{{ $value }}" {{ (string) $priceMaxValue === (string) $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        {{-- 選択後に検索ボタンを押したときだけ送信 --}}
                        <button
                            type="submit"
                            class="ml-2 px-6 py-3 rounded-lg bg-orange-500 text-white font-semibold hover:bg-orange-600 transition-colors"
                        >
                            検索
                        </button>
                    </div>
                </div>

                {{-- クリアボタン --}}
                <div class="flex items-end">
                    <a href="{{ $clearUrl }}" class="px-6 py-3 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                        クリア
                    </a>
                </div>
            </form>
        </div>

        <div class="text-sm text-gray-600 mb-4">
            {{ $listings->total() }} 件中 {{ $listings->firstItem() ?? 0 }} - {{ $listings->lastItem() ?? 0 }} 件表示
        </div>

        @if($listings->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">スキルが見つかりませんでした</h3>
                <p class="text-gray-600">まだスキルは出品されていません</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($listings as $l)
                    @php
                        $isOwnListing = $viewerFreelancer
                            && (int) $viewerFreelancer->id === (int) $l->freelancer_id;
                    @endphp
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                        <a href="{{ route('skills.show', ['skill_listing' => $l->id]) }}" class="block">
                            <div class="relative h-52 overflow-hidden">
                                <img src="{{ $l->thumbnail_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $l->title }}" class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    @php $seller = $l->freelancer; @endphp
                                    @php
                                        $iconPath = $seller?->icon_path ?? null;
                                        $sellerInitial = mb_substr($seller->display_name ?? 'U', 0, 1);
                                        $avatarSrc = null;

                                        if (!empty($iconPath)) {
                                            if (str_starts_with($iconPath, 'http://') || str_starts_with($iconPath, 'https://')) {
                                                $avatarSrc = $iconPath;
                                            } else {
                                                // icon_path が `freelancer_icons/...` や `storage/freelancer_icons/...` のどちらでも対応
                                                $iconRel = ltrim((string) $iconPath, '/');
                                                if (str_starts_with($iconRel, 'storage/')) {
                                                    $iconRel = substr($iconRel, strlen('storage/'));
                                                }
                                                $avatarSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($iconRel);
                                            }
                                        }
                                    @endphp
                                    @if(!empty($avatarSrc))
                                        <img src="{{ $avatarSrc }}" alt="{{ $seller->display_name }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-[#E5E7EB] flex items-center justify-center text-[#374151] font-bold">
                                            {{ $sellerInitial }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="font-bold text-sm text-gray-900">{{ $seller->display_name ?? '出品者' }}</div>
                                        <div class="text-xs text-gray-500">職種: {{ $seller->job_title ?? '-' }}</div>
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $l->title }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($l->description, 100) }}</p>
                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach($l->skills->take(3) as $skill)
                                        <span class="px-2 py-1 bg-purple-50 text-purple-600 text-xs rounded-full">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <span class="font-bold text-sm">{{ $l->rating_average ?? '0' }}</span>
                                        <span class="text-xs text-gray-500">({{ $l->reviews_count ?? 0 }})</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-purple-600">¥{{ number_format($l->price) }}</div>
                                        @if($l->delivery_days)
                                            <div class="text-xs text-gray-500">納期: {{ $l->delivery_days }}日</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>

                        @if($isOwnListing)
                            <div class="px-6 pb-4 pt-0 flex flex-wrap gap-2 border-t border-gray-100">
                                <a href="{{ route('skills.edit', array_merge(['skill_listing' => $l->id], request()->filled('slot') ? ['slot' => request('slot')] : [])) }}"
                                   class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors"
                                   onclick="event.stopPropagation();">
                                    編集
                                </a>
                                <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors"
                                        onclick="openSkillDeleteModal({{ $l->id }}, {{ json_encode($l->title) }}, {{ json_encode(route('skills.destroy', array_merge(['skill_listing' => $l->id], request()->filled('slot') ? ['slot' => request('slot')] : []))) }});">
                                    削除
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @php
                $pLast = $listings->lastPage();
                $pCur = $listings->currentPage();
                $listingsPaginationElements = [];

                if ($pLast <= 1) {
                    if ($pLast === 1) {
                        $listingsPaginationElements[] = ['type' => 'page', 'n' => 1];
                    }
                } elseif ($pLast <= 15) {
                    for ($n = 1; $n <= $pLast; $n++) {
                        $listingsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } elseif ($pCur <= 7) {
                    $upto = min(13, $pLast);
                    for ($n = 1; $n <= $upto; $n++) {
                        $listingsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($upto < $pLast) {
                        $listingsPaginationElements[] = ['type' => 'ellipsis'];
                        $listingsPaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                } elseif ($pCur >= $pLast - 6) {
                    $listingsPaginationElements[] = ['type' => 'page', 'n' => 1];
                    $listingsPaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pLast - 12);
                    for ($n = $from; $n <= $pLast; $n++) {
                        $listingsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } else {
                    $listingsPaginationElements[] = ['type' => 'page', 'n' => 1];
                    $listingsPaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pCur - 6);
                    $to = min($pLast - 1, $pCur + 6);
                    for ($n = $from; $n <= $to; $n++) {
                        $listingsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($to < $pLast) {
                        if ($to + 1 < $pLast) {
                            $listingsPaginationElements[] = ['type' => 'ellipsis'];
                        }
                        $listingsPaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                }
            @endphp

            @if($pLast >= 1 && count($listingsPaginationElements) > 0)
                <nav class="profiles-pagination-bar mt-8" aria-label="ページ送り">
                    @if($listings->onFirstPage())
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&lt;</span>
                    @else
                        <a href="{{ $listings->previousPageUrl() }}" class="profiles-page-nav" rel="prev" aria-label="前のページ">&lt;</a>
                    @endif

                    @foreach($listingsPaginationElements as $el)
                        @if($el['type'] === 'ellipsis')
                            <span class="profiles-page-ellipsis" aria-hidden="true">...</span>
                        @else
                            @if($el['n'] === $pCur)
                                <span class="profiles-page-link profiles-page-active">{{ $el['n'] }}</span>
                            @else
                                <a href="{{ $listings->url($el['n']) }}" class="profiles-page-link">{{ $el['n'] }}</a>
                            @endif
                        @endif
                    @endforeach

                    @if($listings->hasMorePages())
                        <a href="{{ $listings->nextPageUrl() }}" class="profiles-page-nav" rel="next" aria-label="次のページ">&gt;</a>
                    @else
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&gt;</span>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</div>

<div id="skillDeleteModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
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

    function openSkillDeleteModal(skillId, skillTitle, destroyUrl) {
        currentSkillDeleteUrl = destroyUrl;
        const modal = document.getElementById('skillDeleteModal');
        const message = document.getElementById('skillDeleteModalMessage');
        const form = document.getElementById('skillDeleteForm');
        if (!modal || !message || !form) return;

        message.textContent = '「' + skillTitle + '」を本当に削除しますか？この操作は取り消せません。';
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
</script>
@endsection
