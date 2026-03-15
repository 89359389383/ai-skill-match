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
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">記事</h1>
                    <p class="text-gray-600">AIに関する知識や経験を共有する記事一覧</p>
                </div>
                <a href="{{ route('articles.create') }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    記事を投稿
                </a>
            </div>
        </div>

        @if($articles->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">まだ記事がありません</h3>
                <p class="text-gray-600 mb-6">最初の記事を投稿してみましょう</p>
                <a href="{{ route('articles.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                    記事を投稿する
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $a)
                    <a href="{{ route('articles.show', ['article' => $a->id]) }}" class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $a->eyecatch_image_url ?? 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&h=600&fit=crop' }}" alt="{{ $a->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full">{{ $a->category ?? 'その他' }}</span>
                                @foreach($a->tags->take(2) as $tag)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $a->title }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ Str::limit($a->excerpt ?? '', 80) }}</p>
                            @php $authorF = $a->user?->freelancer; @endphp
                            <div class="flex items-center gap-3">
                                <img src="{{ $authorF?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                <div class="text-sm text-gray-600">{{ $authorF?->display_name ?? $a->user?->email ?? '匿名' }}</div>
                                <div class="text-sm text-gray-500 ml-auto">{{ $a->published_at?->format('Y/m/d') ?? $a->created_at?->format('Y/m/d') }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
