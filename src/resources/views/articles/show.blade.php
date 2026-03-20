@extends('layouts.public')

@section('title', ($article->title ?? '記事') . ' - AIスキルマッチ')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
.prose p { margin-bottom: 1rem; line-height: 1.75; }
.prose ul { list-style-type: disc; padding-left: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }
.article-body .ql-editor { min-height: auto; padding: 0; font-size: 1.05rem; line-height: 1.75; }
.article-body .ql-editor h1 { font-size: 1.875rem; font-weight: 700; margin: 1rem 0; }
.article-body .ql-editor h2 { font-size: 1.5rem; font-weight: 700; margin: 1rem 0; }
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

                @php $authorF = $article->user?->freelancer; @endphp
                <div class="flex items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <img src="{{ $authorF?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="w-14 h-14 rounded-full object-cover">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $authorF?->display_name ?? $article->user?->email ?? '匿名' }}</div>
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
                        <div class="ql-snow article-body border-0">
                            <div class="ql-editor text-gray-800">
                                {!! $article->body_html !!}
                            </div>
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
