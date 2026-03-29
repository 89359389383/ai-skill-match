@extends('layouts.public')

@section('title', 'AIプロフェッショナル - AIスキルマッチ')

@push('styles')
<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.profile-skill-search-form {
    max-width: 600px;
    min-height: 60px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

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
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">AIプロフェッショナル</h1>
                <p class="text-gray-600">経験豊富なAIスペシャリストと繋がろう</p>
            </div>
        </div>

        <div class="mb-8">
            <form action="{{ route('profiles.index') }}" method="GET" class="profile-skill-search-form flex items-center gap-2 bg-gray-200 rounded-lg px-4 py-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-gray-500 flex-shrink-0">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <input
                    type="text"
                    name="skill"
                    value="{{ request('skill') }}"
                    placeholder="n8nなど スキル名で検索"
                    class="flex-1 bg-transparent border-none outline-none text-gray-700 placeholder-gray-500"
                >
                @if(request('skill'))
                    <a href="{{ route('profiles.index') }}" class="text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">
                        クリア
                    </a>
                @endif
            </form>
        </div>

        @if($freelancers->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">プロフィールが見つかりませんでした</h3>
                <p class="text-gray-600">まだフリーランスが登録されていません</p>
            </div>
        @else
            <p class="text-sm text-gray-600 mb-4">
                {{ number_format($freelancers->total()) }} 人中
                {{ number_format($freelancers->firstItem()) }} - {{ number_format($freelancers->lastItem()) }}
                人表示
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($freelancers as $f)
                    <a href="{{ route('profiles.show', ['user' => $f->user_id]) }}" class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                        <div class="h-24 bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 max-h-[65px]"></div>
                        <div class="relative px-6">
                            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                                @php
                                    $iconPath = $f->icon_path ?? null;
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

                                    // freelancers.min_rate は「万円」単位で保存されている前提。
                                    $minRate = (int) ($f->min_rate ?? 0);
                                    $maxRate = (int) ($f->max_rate ?? 0);
                                    $minRateManStr = number_format($minRate, 0);
                                    $maxRateManStr = number_format($maxRate, 0);
                                @endphp

                                <img src="{{ $iconSrc ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop' }}"
                                     alt="{{ $f->display_name }}"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                            </div>
                        </div>
                        <div class="pt-16 px-6 pb-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $f->display_name ?? '名前未設定' }}</h3>
                            <p class="text-sm text-gray-600 mb-2">職種: {{ $f->job_title ?? '未設定' }}</p>
                            @php
                                $skillListings = $f->skillListings ?? collect();
                                // 全レビューを合算した重み付き平均
                                // rating_average は「各出品サービスの平均点」、reviews_count は「そのサービスのレビュー件数」
                                $reviewsCountTotal = (int) ($skillListings->sum('reviews_count') ?? 0);
                                $weightedSum = (float) $skillListings->reduce(function ($carry, $sl) {
                                    $rating = (float) ($sl->rating_average ?? 0);
                                    $count = (int) ($sl->reviews_count ?? 0);
                                    return $carry + ($rating * $count);
                                }, 0.0);
                                $avgRating = $reviewsCountTotal > 0 ? ($weightedSum / $reviewsCountTotal) : 0.0;
                                $avgRatingFormatted = number_format(round($avgRating, 1), 1, '.', '');
                            @endphp
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-yellow-400 fill-yellow-400 flex-shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <span class="font-bold text-gray-900">{{ $avgRatingFormatted }}</span>
                                <span class="text-sm text-gray-500">({{ $reviewsCountTotal }}件)</span>
                            </div>
                            @php
                                $allSkills = $f->skills->pluck('name')->merge($f->customSkills->pluck('name'))->values();
                            @endphp
                            <div class="mb-3">
                                <div class="flex flex-wrap gap-2 justify-center">
                                    @forelse($allSkills->take(3) as $skillName)
                                        <span class="px-3 py-1 bg-orange-500 text-white text-xs font-medium rounded-full">{{ $skillName }}</span>
                                    @empty
                                        <span class="text-xs text-gray-500">スキル未設定</span>
                                    @endforelse
                                    @if($allSkills->count() > 3)
                                        <span class="px-3 py-1 bg-orange-500 text-white text-xs font-medium rounded-full">+{{ $allSkills->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                                <p class="text-sm mb-3">
                                    <span class="font-bold text-gray-700">希望単価: </span>
                                    <span class="font-bold text-orange-600">
                                        @if($maxRate > 0)
                                            {{ $minRateManStr }}万〜{{ $maxRateManStr }}万
                                        @else
                                            {{ $minRateManStr }}万
                                        @endif
                                    </span>
                                </p>
                            <p class="text-sm text-gray-600 mb-0 line-clamp-3">{{ Str::limit($f->bio ?? '', 100) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            @php
                $pLast = $freelancers->lastPage();
                $pCur = $freelancers->currentPage();
                $profilePaginationElements = [];

                if ($pLast <= 1) {
                    if ($pLast === 1) {
                        $profilePaginationElements[] = ['type' => 'page', 'n' => 1];
                    }
                } elseif ($pLast <= 15) {
                    for ($n = 1; $n <= $pLast; $n++) {
                        $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } elseif ($pCur <= 7) {
                    $upto = min(13, $pLast);
                    for ($n = 1; $n <= $upto; $n++) {
                        $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($upto < $pLast) {
                        $profilePaginationElements[] = ['type' => 'ellipsis'];
                        $profilePaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                } elseif ($pCur >= $pLast - 6) {
                    $profilePaginationElements[] = ['type' => 'page', 'n' => 1];
                    $profilePaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pLast - 12);
                    for ($n = $from; $n <= $pLast; $n++) {
                        $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } else {
                    $profilePaginationElements[] = ['type' => 'page', 'n' => 1];
                    $profilePaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pCur - 6);
                    $to = min($pLast - 1, $pCur + 6);
                    for ($n = $from; $n <= $to; $n++) {
                        $profilePaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($to < $pLast) {
                        if ($to + 1 < $pLast) {
                            $profilePaginationElements[] = ['type' => 'ellipsis'];
                        }
                        $profilePaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                }
            @endphp

            @if($pLast >= 1 && count($profilePaginationElements) > 0)
                <nav class="profiles-pagination-bar mt-8" aria-label="ページ送り">
                    @if($freelancers->onFirstPage())
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&lt;</span>
                    @else
                        <a href="{{ $freelancers->previousPageUrl() }}" class="profiles-page-nav" rel="prev" aria-label="前のページ">&lt;</a>
                    @endif

                    @foreach($profilePaginationElements as $el)
                        @if($el['type'] === 'ellipsis')
                            <span class="profiles-page-ellipsis" aria-hidden="true">...</span>
                        @else
                            @if($el['n'] === $pCur)
                                <span class="profiles-page-link profiles-page-active">{{ $el['n'] }}</span>
                            @else
                                <a href="{{ $freelancers->url($el['n']) }}" class="profiles-page-link">{{ $el['n'] }}</a>
                            @endif
                        @endif
                    @endforeach

                    @if($freelancers->hasMorePages())
                        <a href="{{ $freelancers->nextPageUrl() }}" class="profiles-page-nav" rel="next" aria-label="次のページ">&gt;</a>
                    @else
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&gt;</span>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</div>
@endsection
