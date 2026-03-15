@extends('layouts.public')

@section('title', ($article->title ?? '投稿記事') . ' - AIスキルマッチ')

@push('styles')
<style>
.prose p { margin-bottom: 1rem; line-height: 1.75; }
.prose ul { list-style-type: disc; padding-left: 1.5rem; }
.prose li { margin-bottom: 0.5rem; }
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
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <span class="px-4 py-1.5 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">{{ $article->category ?? 'その他' }}</span>
                    @foreach($article->tags as $tag)
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                    @endforeach
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $article->title }}</h1>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="text-sm text-gray-500">
                        作成: {{ $article->created_at?->format('Y年n月j日') }}
                        @if($article->updated_at && $article->updated_at->ne($article->created_at))
                            / 更新: {{ $article->updated_at->format('Y年n月j日') }}
                        @endif
                    </div>
                    <a href="{{ route('my-articles.edit', ['article' => $article->id]) }}" class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>編集</span>
                    </a>
                </div>

                <div class="prose max-w-none mb-8">
                    @if($article->excerpt)
                        <p class="text-lg text-gray-700 leading-relaxed mb-8">{{ $article->excerpt }}</p>
                    @endif

                    @if($article->structure && is_array($article->structure))
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
