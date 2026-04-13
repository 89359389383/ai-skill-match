@extends('layouts.public')

@section('title', ($article->title ?? '投稿記事') . ' - AIスキルマッチ')

@push('styles')
<style>
.prose p { margin-bottom: 1rem; line-height: 1.75; font-size: 20px; }
.prose ul { list-style-type: disc; padding-left: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }

.article-body {
    /* preview で適用された font-size に合わせる（li は継承） */
    font-size: 1.25rem;
    line-height: 1.75;
    color: #1f2937;
    padding: 0;
    margin: 0;
    /* 改行は維持しつつ、行頭の余計なスペースは折りたたむ */
    white-space: pre-line;
    word-break: break-word;
}
.article-body > *:first-child { margin-top: 0; }
.article-body p { margin: 0 0 1rem 0; text-indent: 0 !important; }
.article-body p:last-child { margin-bottom: 0; }
.article-body h1 { font-size: 1.875rem; font-weight: 700; margin: 1rem 0; }
.article-body h2 { font-size: 1.625rem; font-weight: 700; margin: 1rem 0; }
.article-body h3 { font-size: 1.375rem; font-weight: 700; margin: 0.75rem 0; }
.article-body ul { list-style-type: disc; padding-left: 1.5rem; margin: 0 0 1rem 0; }
.article-body ol { list-style-type: decimal; padding-left: 1.5rem; margin: 0 0 1rem 0; }
.article-body ul,
.article-body ol,
.article-body li { white-space: normal; }
.article-body li { margin-bottom: 0.5rem; }
.article-body a { color: #4f46e5; text-decoration: underline; }
.article-body img { max-width: 100%; height: auto; }
.article-body blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; margin: 1rem 0; color: #4b5563; }

.article-toc {
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    padding: 1rem;
    border-radius: 0.75rem;
    margin: 1rem 0;
}
.article-toc ul {
    list-style: none !important;
    list-style-type: none !important;
    padding-left: 0 !important;
    margin: 0.5rem 0 0 0;
}
.article-toc li { margin: 0.25rem 0; }
.article-toc a {
    color: #4f46e5;
    text-decoration: underline;
}

.article-body .ql-editor,
.article-body .ql-snow,
.article-body .ql-container {
    border: none !important;
    box-shadow: none !important;
    box-sizing: border-box !important;
    padding: 0 !important;
    margin: 0 !important;
    min-height: 0 !important;
}
.article-body [class*="ql-indent"] { padding-left: 0 !important; margin-left: 0 !important; }

.article-body iframe {
    max-width: 100%;
}

.article-body hr {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin: 1.5rem 0;
}

/* OGPリンクカード（note風：保存済みHTMLの ogp-card を整形） */
.article-body .ogp-card {
    display: flex;
    flex-direction: row;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    text-decoration: none;
    color: inherit;
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}
.article-body .ogp-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.10);
    transform: translateY(-1px);
}
.article-body .ogp-card-media {
    width: 176px;
    min-height: 132px;
    background: #f3f4f6;
    flex-shrink: 0;
    overflow: hidden;
}
.article-body .ogp-card-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.article-body .ogp-card-media-placeholder {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}
.article-body .ogp-card-content {
    min-width: 0;
    flex: 1;
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 4px;
}
.article-body .ogp-card-site {
    font-size: 12px;
    font-weight: 700;
    color: #4f46e5;
    line-height: 1.2;
}
.article-body .ogp-card-title {
    font-size: 15px;
    font-weight: 800;
    color: #111827;
    line-height: 1.45;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.article-body .ogp-card-description {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.45;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.article-body .ogp-card-domain {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.2;
    margin-top: 2px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.article-body .ogp-card-loading {
    cursor: default;
}
.article-body .ogp-skeleton {
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
}
.article-body .ogp-skeleton::after {
    content: '';
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
    animation: ogp-shimmer 1.3s infinite;
}
.article-body .ogp-skeleton-title {
    width: 75%;
    height: 18px;
    margin-top: 2px;
}
.article-body .ogp-skeleton-line {
    width: 100%;
    height: 12px;
}
.article-body .ogp-skeleton-line.short {
    width: 55%;
}
@keyframes ogp-shimmer {
    100% { transform: translateX(100%); }
}

@media (max-width: 640px) {
    .article-body .ogp-card {
        flex-direction: column;
    }

    .article-body .ogp-card-media {
        width: 100%;
        height: 180px;
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('my-articles.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            投稿記事一覧に戻る
        </a>

        <article class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            @if($article->eyecatch_image_url)
                <div class="aspect-video w-full overflow-hidden">
                    <img src="{{ $article->eyecatch_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-8 md:p-12">
                @php
                    $isOwner = auth()->check() && auth()->user()->id === (int) $article->user_id;
                @endphp
                    @php
                        $author = $article->user ?? null;
                        $authorF = $author?->freelancer;
                        $authorCompany = $author?->company;
                        $isCompanyAuthor = $authorCompany !== null;

                        $displayName = '匿名';
                        $avatarSrc = null;

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
                        } elseif ($authorCompany !== null) {
                            $displayName = $authorCompany->contact_name
                                ?: ($author->name ?? null)
                                ?: $authorCompany->name
                                ?: ($author->email ?? '匿名');

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
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <span class="px-4 py-1.5 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">{{ $article->category ?? 'その他' }}</span>
                    @foreach($article->tags as $tag)
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                    @endforeach
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $article->title }}</h1>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="text-sm text-gray-500 w-full">
                        <div class="flex items-start gap-3">
                            @if($avatarSrc)
                                @if($isCompanyAuthor)
                                    <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                                        <img src="{{ $avatarSrc }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <a href="{{ route('profiles.show', ['user' => $article->user_id]) }}" class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                                        <img src="{{ $avatarSrc }}" alt="" class="w-full h-full object-cover">
                                    </a>
                                @endif
                            @else
                                @if($isCompanyAuthor)
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-base font-bold flex-shrink-0">
                                        {{ $authorInitial }}
                                    </div>
                                @else
                                    <a href="{{ route('profiles.show', ['user' => $article->user_id]) }}" class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-base font-bold flex-shrink-0">
                                        {{ $authorInitial }}
                                    </a>
                                @endif
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    @if($isCompanyAuthor)
                                        <div class="font-semibold text-gray-900 truncate min-w-0">
                                            {{ $displayName }}
                                        </div>
                                    @else
                                        <a href="{{ route('profiles.show', ['user' => $article->user_id]) }}"
                                           class="font-semibold text-gray-900 truncate hover:text-orange-600 transition-colors min-w-0">
                                            {{ $displayName }}
                                        </a>
                                    @endif
                                    <a href="{{ route('articles.index', ['user' => $article->user_id]) }}"
                                       class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline whitespace-nowrap">
                                        この著者の記事一覧
                                    </a>
                                </div>

                                <div class="mt-1">
                                    作成: {{ $article->created_at?->format('Y年n月j日') }}
                                    @if($article->updated_at && $article->updated_at->ne($article->created_at))
                                        / 更新: {{ $article->updated_at->format('Y年n月j日') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($isOwner)
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('my-articles.edit', ['article' => $article->id]) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>編集</span>
                            </a>

                            <form action="{{ route('my-articles.destroy', ['article' => $article->id]) }}" method="POST" onsubmit="return confirm('この記事を削除しますか？この操作は元に戻せません。');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium bg-red-600 text-white hover:bg-red-700 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>削除</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="max-w-none mb-8">
                    @if($article->excerpt)
                        <p class="text-lg text-gray-700 leading-relaxed mb-8">{{ $article->excerpt }}</p>
                    @endif

                    @if(filled($article->body_html))
                        <div class="article-body">
                            {!! $article->body_html !!}
                        </div>
                    @elseif($article->structure && is_array($article->structure))
                        <div class="prose max-w-none">
                            @foreach($article->structure as $section)
                                @if(is_array($section))
                                    <div class="mb-8">
                                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $section['title'] ?? '' }}</h2>
                                        @if(isset($section['subsections']) && is_array($section['subsections']))
                                            @foreach($section['subsections'] as $sub)
                                                <div class="mb-6">
                                                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $sub['title'] ?? '' }}</h3>
                                                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $sub['content'] ?? '' }}</p>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-700 leading-relaxed">記事の本文はありません。</p>
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-8 border-t border-gray-200">
                    <span class="text-sm text-gray-600">{{ $article->views_count ?? 0 }} 回閲覧</span>
                    <span class="text-sm text-gray-600">{{ $article->likes_count ?? 0 }} いいね</span>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
