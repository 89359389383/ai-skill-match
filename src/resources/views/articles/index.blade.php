@extends('layouts.public')

@section('title', '記事一覧 - AIスキルマッチ')

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
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
@php
    $viewer = auth('freelancer')->user() ?? auth('company')->user();
@endphp
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    @php
                        // `?user=...` で絞り込まれている場合、一覧に含まれる最初の著者名から表示用の見出しを作る
                        $targetDisplayName = null;
                        if (request()->filled('user') && !$articles->isEmpty()) {
                            $targetArticle = $articles->first();
                            $targetDisplayName = $targetArticle?->user?->freelancer?->display_name
                                ?? $targetArticle?->user?->email
                                ?? null;
                        }
                    @endphp

                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        {{ $targetDisplayName ? ($targetDisplayName . 'さんの記事一覧') : '記事一覧' }}
                    </h1>
                    @if(request()->filled('user'))
                        <p class="text-sm text-indigo-600 mt-2">特定ユーザーの公開記事のみ表示しています。<a href="{{ route('articles.index') }}" class="underline font-medium">すべての記事を見る</a></p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($viewer)
                        <a href="{{ route('my-articles.index') }}" class="flex items-center gap-2 px-5 py-3 border-2 border-indigo-200 text-indigo-700 rounded-xl font-semibold hover:bg-indigo-50 transition-all">
                            自分の記事一覧
                        </a>
                    @endif
                    <a href="{{ route('articles.create') }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        記事を投稿
                    </a>
                </div>
            </div>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if($articles->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">まだ記事がありません</h3>
                <p class="text-gray-600 mb-6">最初の記事を投稿してみましょう</p>
                @if($viewer)
                    <a href="{{ route('articles.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                        記事を投稿する
                    </a>
                @endif
            </div>
        @else
            <p class="text-sm text-gray-600 mb-4">
                {{ number_format($articles->total()) }} 件中
                {{ number_format($articles->firstItem()) }} - {{ number_format($articles->lastItem()) }} 件表示
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $a)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden flex flex-col">
                        <a href="{{ route('articles.show', ['article' => $a->id]) }}" class="block flex-1">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $a->eyecatch_image_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $a->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-[auto_1fr] items-start gap-3 mb-2">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full">{{ $a->category ?? 'その他' }}</span>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($a->tags->take(2) as $tag)
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $a->title }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($a->excerpt ?? '', 80) }}</p>
                                @php
                                    $author = $a->user;
                                    $authorF = $author?->freelancer;
                                    $authorCompany = $author?->company;
                                    $displayName = '匿名';
                                    $avatarSrc = null;
                                    $isCompanyAuthor = $authorCompany !== null;

                                    if ($authorF) {
                                        $displayName = $authorF->display_name ?? $author->email ?? '匿名';
                                        $iconPath = $authorF->icon_path ?? null;
                                        if (!empty($iconPath)) {
                                            if (str_starts_with($iconPath, 'http://') || str_starts_with($iconPath, 'https://')) {
                                                $avatarSrc = $iconPath;
                                            } else {
                                                $iconRel = ltrim($iconPath, '/');
                                                if (str_starts_with($iconRel, 'storage/')) {
                                                    $iconRel = substr($iconRel, strlen('storage/'));
                                                }
                                                $avatarSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($iconRel);
                                            }
                                        }
                                    } elseif ($isCompanyAuthor) {
                                        $displayName = $authorCompany->contact_name
                                            ?: ($author->name ?? null)
                                            ?: $authorCompany->name
                                            ?: ($author->email ?? '匿名');
                                        // 企業アイコンがあれば表示する
                                        $iconPath = $authorCompany->icon_path ?? null;
                                        if (!empty($iconPath)) {
                                            if (str_starts_with($iconPath, 'http://') || str_starts_with($iconPath, 'https://')) {
                                                $avatarSrc = $iconPath;
                                            } else {
                                                $iconRel = ltrim($iconPath, '/');
                                                if (str_starts_with($iconRel, 'storage/')) {
                                                    $iconRel = substr($iconRel, strlen('storage/'));
                                                }
                                                $avatarSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($iconRel);
                                            }
                                        }
                                    } elseif ($author) {
                                        $displayName = $author->name ?? $author->email ?? '匿名';
                                    }

                                    $authorInitial = mb_substr($displayName, 0, 1);
                                @endphp
                                <div class="flex items-center gap-3">
                                    @if($avatarSrc)
                                        <img src="{{ $avatarSrc }}" alt="" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ $authorInitial }}
                                        </div>
                                    @endif
                                    <div class="text-sm text-gray-600 min-w-0 truncate">{{ $displayName }}</div>
                                    <div class="text-sm text-gray-500 ml-auto flex-shrink-0">{{ $a->published_at?->format('Y/m/d') ?? $a->created_at?->format('Y/m/d') }}</div>
                                </div>
                            </div>
                        </a>
                        @if($viewer && (int) $viewer->id === (int) $a->user_id)
                            <div class="px-6 pb-4 pt-0 flex flex-wrap gap-2 border-t border-gray-100 mt-auto">
                                <a href="{{ route('my-articles.edit', ['article' => $a->id]) }}" onclick="event.stopPropagation();" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors">
                                    編集
                                </a>
                                <button type="button" onclick="event.stopPropagation(); openArticleDeleteModal({{ $a->id }})" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                                    削除
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @php
                $pLast = $articles->lastPage();
                $pCur = $articles->currentPage();
                $articlesPaginationElements = [];

                if ($pLast <= 1) {
                    if ($pLast === 1) {
                        $articlesPaginationElements[] = ['type' => 'page', 'n' => 1];
                    }
                } elseif ($pLast <= 15) {
                    for ($n = 1; $n <= $pLast; $n++) {
                        $articlesPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } elseif ($pCur <= 7) {
                    $upto = min(13, $pLast);
                    for ($n = 1; $n <= $upto; $n++) {
                        $articlesPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($upto < $pLast) {
                        $articlesPaginationElements[] = ['type' => 'ellipsis'];
                        $articlesPaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                } elseif ($pCur >= $pLast - 6) {
                    $articlesPaginationElements[] = ['type' => 'page', 'n' => 1];
                    $articlesPaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pLast - 12);
                    for ($n = $from; $n <= $pLast; $n++) {
                        $articlesPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                } else {
                    $articlesPaginationElements[] = ['type' => 'page', 'n' => 1];
                    $articlesPaginationElements[] = ['type' => 'ellipsis'];
                    $from = max(2, $pCur - 6);
                    $to = min($pLast - 1, $pCur + 6);
                    for ($n = $from; $n <= $to; $n++) {
                        $articlesPaginationElements[] = ['type' => 'page', 'n' => $n];
                    }
                    if ($to < $pLast) {
                        if ($to + 1 < $pLast) {
                            $articlesPaginationElements[] = ['type' => 'ellipsis'];
                        }
                        $articlesPaginationElements[] = ['type' => 'page', 'n' => $pLast];
                    }
                }
            @endphp

            @if($pLast >= 1 && count($articlesPaginationElements) > 0)
                <nav class="profiles-pagination-bar mt-8" aria-label="ページ送り">
                    @if($articles->onFirstPage())
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&lt;</span>
                    @else
                        <a href="{{ $articles->previousPageUrl() }}" class="profiles-page-nav" rel="prev" aria-label="前のページ">&lt;</a>
                    @endif

                    @foreach($articlesPaginationElements as $el)
                        @if($el['type'] === 'ellipsis')
                            <span class="profiles-page-ellipsis" aria-hidden="true">...</span>
                        @else
                            @if($el['n'] === $pCur)
                                <span class="profiles-page-link profiles-page-active">{{ $el['n'] }}</span>
                            @else
                                <a href="{{ $articles->url($el['n']) }}" class="profiles-page-link">{{ $el['n'] }}</a>
                            @endif
                        @endif
                    @endforeach

                    @if($articles->hasMorePages())
                        <a href="{{ $articles->nextPageUrl() }}" class="profiles-page-nav" rel="next" aria-label="次のページ">&gt;</a>
                    @else
                        <span class="profiles-page-nav profiles-page-nav-disabled" aria-disabled="true">&gt;</span>
                    @endif
                </nav>
            @endif
        @endif
    </div>
</div>

@if($viewer)
<div id="articleDeleteModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-2">記事を削除しますか？</h3>
        <p class="text-sm text-gray-600 mb-6">この操作は取り消せません。</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeArticleDeleteModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">キャンセル</button>
            <form id="articleDeleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700">削除する</button>
            </form>
        </div>
    </div>
</div>

<script>
function openArticleDeleteModal(articleId) {
    var form = document.getElementById('articleDeleteForm');
    form.action = '{{ url('/my-articles') }}/' + articleId;
    var modal = document.getElementById('articleDeleteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeArticleDeleteModal() {
    var modal = document.getElementById('articleDeleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('articleDeleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeArticleDeleteModal();
});
</script>
@endif
@endsection
