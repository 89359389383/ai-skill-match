@extends('layouts.public')

@section('title', ($article->title ?? '記事') . ' - AIスキルマッチ')

@push('styles')
<style>
/* 旧「構造」表示用 */
.prose p { margin-bottom: 1rem; line-height: 1.75; }
.prose ul { list-style-type: disc; padding-left: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }

/* 本文（Quill 非依存。保存 HTML 内の ql-* も無効化） */
.article-body {
    font-size: 1.05rem;
    line-height: 1.75;
    color: #1f2937;
    padding: 0;
    margin: 0;
    /* 改行は維持しつつ、行頭のスペースによるズレを抑える */
    white-space: pre-line;
    word-break: break-word;
}
.article-body > *:first-child { margin-top: 0; }
.article-body p { margin: 0 0 1rem 0; text-indent: 0 !important; }
.article-body p:last-child { margin-bottom: 0; }
.article-body h1 { font-size: 1.875rem; font-weight: 700; margin: 1rem 0; }
.article-body h2 { font-size: 1.5rem; font-weight: 700; margin: 1rem 0; }
.article-body h3 { font-size: 1.25rem; font-weight: 700; margin: 0.75rem 0; }
.article-body ul { list-style-type: disc; padding-left: 1.5rem; margin: 0 0 1rem 0; }
.article-body ol { list-style-type: decimal; padding-left: 1.5rem; margin: 0 0 1rem 0; }
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
    list-style: none;
    padding-left: 0;
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
</style>
@endpush

@section('content')
@php
    $currentUserId = null;
    if (auth('freelancer')->check()) {
        $currentUserId = (int) auth('freelancer')->user()->id;
    } elseif (auth('company')->check()) {
        $currentUserId = (int) auth('company')->user()->id;
    }
@endphp
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm text-gray-500">
            <a href="{{ route('top') }}" class="hover:text-gray-900">ホーム</a>
            <span class="mx-2">></span>
            <a href="{{ route('articles.index') }}" class="hover:text-gray-900">記事</a>
            <span class="mx-2">></span>
            <div class="inline-flex flex-wrap items-center gap-3">
                <span class="font-bold text-gray-900">{{ Str::limit($article->title, 40) }}</span>
                @if($currentUserId !== null && $currentUserId === (int) $article->user_id)
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('my-articles.edit', ['article' => $article->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            編集
                        </a>
                        <form action="{{ route('my-articles.destroy', ['article' => $article->id]) }}" method="POST" class="inline" onsubmit="return confirm('この記事を削除しますか？この操作は元に戻せません。');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                削除
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </nav>

        <article class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if($article->eyecatch_image_url)
                <div class="aspect-video w-full overflow-hidden">
                    <img src="{{ $article->eyecatch_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-8 md:p-12">
                <div class="grid grid-cols-[auto_1fr] items-center gap-3 mb-6">
                    <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-full">{{ $article->category ?? 'その他' }}</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $article->title }}</h1>

                @php
                    $author = $article->user;
                    $authorF = $author?->freelancer;
                    $authorCompany = $author?->company;
                    $displayName = '匿名';
                    $avatarSrc = null;
                    $isCompanyAuthor = $authorCompany !== null;
                    $isOwner = $currentUserId !== null && $currentUserId === (int) $article->user_id;

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

                        // 企業アイコン（storage配下の相対パス前提でURL化）
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

                <div class="flex items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-3 min-w-0">
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="" class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-lg font-bold flex-shrink-0">
                                {{ $authorInitial }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 truncate">{{ $displayName }}</div>
                            <div class="text-sm text-gray-500">{{ $article->published_at?->format('Y年n月j日') ?? $article->created_at?->format('Y年n月j日') }} 公開</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $article->views_count ?? 0 }} 回閲覧
                    </div>
                </div>

                <div class="max-w-none">
                    @if($article->excerpt)
                        <p class="text-lg text-gray-700 leading-relaxed mb-8 whitespace-pre-line">{{ $article->excerpt }}</p>
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
            </div>
        </article>
    </div>
</div>
@endsection
