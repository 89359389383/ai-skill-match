@extends('layouts.public')

@section('title', 'AI知恵袋 - 質問一覧')

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.questions-tab-active {
    border-bottom-color: #dc2626;
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
<div class="min-h-screen py-12 bg-gray-50 w-full max-w-[900px] mx-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">知恵袋</h1>
                </div>
                <div class="flex items-center gap-3">
                    @if(auth('freelancer')->check() || auth('company')->check())
                        <a href="{{ route('questions.my.index') }}"
                           class="flex items-center gap-2 px-6 py-4 border-2 border-indigo-200 text-indigo-700 rounded-xl font-bold shadow-sm hover:bg-indigo-50 transition-all duration-300 text-lg">
                            自分の質問一覧
                        </a>
                    @endif

                    <a href="{{ route('questions.create') }}"
                       class="flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        質問を投稿
                    </a>
                </div>
            </div>
        </div>

        @php
            $tab = $tab ?? 'open';
        $viewerUserId = null;
        if (auth('freelancer')->check()) {
            $viewerUserId = auth('freelancer')->user()->id ?? null;
        } elseif (auth('company')->check()) {
            $viewerUserId = auth('company')->user()->id ?? null;
        }
        @endphp

        @if(!$questions->isEmpty())
            <p class="text-sm text-gray-600 mb-4">
                {{ number_format($questions->total()) }} 件中
                {{ number_format($questions->firstItem() ?? 0) }} - {{ number_format($questions->lastItem() ?? 0) }} 件表示
            </p>
        @endif

        <div class="bg-white rounded-t-lg border border-gray-200 shadow-sm overflow-hidden">
            <nav class="flex bg-gray-100" aria-label="質問の状態">
                <a href="{{ route('questions.index', ['tab' => 'open']) }}"
                   class="flex-1 text-center py-3.5 text-sm font-semibold border-b-4 transition-colors {{ $tab === 'open' ? 'questions-tab-active text-gray-900 bg-white/60' : 'border-b-transparent text-gray-500 hover:text-gray-700' }}">
                    回答募集中
                </a>
                <div class="w-px bg-gray-300 shrink-0 self-stretch my-3" aria-hidden="true"></div>
                <a href="{{ route('questions.index', ['tab' => 'resolved']) }}"
                   class="flex-1 text-center py-3.5 text-sm font-semibold border-b-4 transition-colors {{ $tab === 'resolved' ? 'questions-tab-active text-gray-900 bg-white/60' : 'border-b-transparent text-gray-500 hover:text-gray-700' }}">
                    解決済み
                </a>
            </nav>
        </div>

        @if($questions->isEmpty())
            <div class="text-center py-16 bg-white rounded-b-lg border border-t-0 border-gray-200 shadow-sm px-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                @if($tab === 'open')
                    <h3 class="text-xl font-bold text-gray-900 mb-2">回答募集中の質問はありません</h3>
                    <p class="text-gray-600 mb-6">最初の質問を投稿してみましょう</p>
                    <a href="{{ route('questions.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                        質問を投稿する
                    </a>
                @else
                    <h3 class="text-xl font-bold text-gray-900 mb-2">解決済みの質問はまだありません</h3>
                    <p class="text-gray-600 mb-6">ベストアンサーが選ばれるとここに表示されます</p>
                    <a href="{{ route('questions.index', ['tab' => 'open']) }}" class="inline-block px-6 py-3 border-2 border-gray-300 text-gray-800 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                        回答募集中を見る
                    </a>
                @endif
            </div>
        @else
            <div class="bg-white rounded-b-lg border border-t-0 border-gray-200 shadow-sm divide-y divide-gray-100">
                @foreach($questions as $q)
                    <a href="{{ route('questions.show', ['question' => $q->id]) }}" class="block px-5 py-4 hover:bg-gray-50/80 transition-colors">
                        <p class="text-[16px] text-gray-900 mb-1.5">{{ $q->category ?? 'その他' }}</p>
                        <div class="flex items-start gap-3">
                            <h2 class="text-lg font-bold text-blue-600 line-clamp-2 mb-2 leading-snug flex-1">{{ $q->title }}</h2>
                            @if($viewerUserId && (int)$q->user_id === (int)$viewerUserId)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-orange-100 text-orange-700 text-[12px] font-bold whitespace-nowrap">
                                    自分の質問
                                </span>
                            @endif
                        </div>
                        @if($tab === 'resolved' && $q->bestAnswer)
                            <div class="ml-1 pl-3 border-l-4 border-gray-200 text-sm text-gray-600 mb-3 leading-relaxed">
                                <span class="font-medium text-gray-700">ベストアンサー：</span>{{ Str::limit(preg_replace('/\s+/', ' ', trim(strip_tags($q->bestAnswer->content))), 140) }}
                            </div>
                        @endif
                        <div class="flex items-center gap-4 text-[18px] text-gray-900 font-semibold">
                            <span class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-70"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                {{ $q->answers_count ?? 0 }}
                            </span>
                            <span>
                                @if($tab === 'resolved')
                                    {{ $q->updated_at?->format('n/j G:i') }}
                                @else
                                    {{ $q->created_at?->format('n/j G:i') }}
                                @endif
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            @php
                $pLast = $questions->lastPage();
                $pCur = $questions->currentPage();
                $questionsPaginationElements = [];

                if ($pLast <= 1) {
                    if ($pLast === 1) {
                        $questionsPaginationElements[] = ['type' => 'page', 'n' => 1];
                    }
                } elseif ($pLast <= 15) {
                    for ($n = 1; $n <= $pLast; $n++) {
                        $questionsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } elseif ($pCur <= 7) {
                    $upto = min(13, $pLast);
                    for ($n = 1; $n <= $upto; $n++) {
                        $questionsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($upto < $pLast) {
                        $questionsPaginationElements[] = ['type' => 'ellipsis'];
                        $questionsPaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                } elseif ($pCur >= $pLast - 6) {
                    $questionsPaginationElements[] = ['type' => 'page', 'n' => 1];
                    $questionsPaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pLast - 12);
                    for ($n = $from; $n <= $pLast; $n++) {
                        $questionsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } else {
                    $questionsPaginationElements[] = ['type' => 'page', 'n' => 1];
                    $questionsPaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pCur - 6);
                    $to = min($pLast - 1, $pCur + 6);
                    for ($n = $from; $n <= $to; $n++) {
                        $questionsPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($to < $pLast) {
                        if ($to + 1 < $pLast) {
                            $questionsPaginationElements[] = ['type' => 'ellipsis'];
                        }
                        $questionsPaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                }
            @endphp

            @if($pLast >= 1 && count($questionsPaginationElements) > 0)
                <nav class="profiles-pagination-bar mt-8" aria-label="ページ送り">
                    @if($questions->onFirstPage())
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&lt;</span>
                    @else
                        <a href="{{ $questions->previousPageUrl() }}" class="profiles-page-nav" rel="prev" aria-label="前のページ">&lt;</a>
                    @endif

                    @foreach($questionsPaginationElements as $el)
                        @if($el['type'] === 'ellipsis')
                            <span class="profiles-page-ellipsis" aria-hidden="true">...</span>
                        @else
                            @if($el['n'] === $pCur)
                                <span class="profiles-page-link profiles-page-active">{{ $el['n'] }}</span>
                            @else
                                <a href="{{ $questions->url($el['n']) }}" class="profiles-page-link">{{ $el['n'] }}</a>
                            @endif
                        @endif
                    @endforeach

                    @if($questions->hasMorePages())
                        <a href="{{ $questions->nextPageUrl() }}" class="profiles-page-nav" rel="next" aria-label="次のページ">&gt;</a>
                    @else
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&gt;</span>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</div>
@endsection
