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
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm text-gray-500">
            <a href="{{ route('top') }}" class="hover:text-gray-900">ホーム</a>
            <span class="mx-2">></span>
            <a href="{{ route('articles.index') }}" class="hover:text-gray-900">記事</a>
            <span class="mx-2">></span>
            <span class="font-bold text-gray-900">{{ Str::limit($article->title, 40) }}</span>
        </nav>

        <article class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if($article->eyecatch_image_url)
                <div class="aspect-video w-full overflow-hidden">
                    <img src="{{ $article->eyecatch_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-8 md:p-12">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-full">{{ $article->category ?? 'その他' }}</span>
                    @foreach($article->tags as $tag)
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                    @endforeach
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $article->title }}</h1>

                @php
                    $author = $article->user;
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
            </div>
        </article>
    </div>
</div>
@endsection
