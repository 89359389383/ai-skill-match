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
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">AI知恵袋</h1>
                    <p class="text-gray-600">AIに関する質問を投稿して、コミュニティから回答を得よう</p>
                </div>
                <a href="{{ route('questions.create') }}" class="flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    質問を投稿
                </a>
            </div>
        </div>

        @if($questions->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">まだ質問がありません</h3>
                <p class="text-gray-600 mb-6">最初の質問を投稿してみましょう</p>
                <a href="{{ route('questions.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                    質問を投稿する
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($questions as $q)
                    <a href="{{ route('questions.show', ['question' => $q->id]) }}" class="block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 p-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex md:flex-col gap-4 md:gap-2 items-center md:items-center text-center">
                                <div class="flex flex-col items-center">
                                    <div class="text-2xl font-bold text-indigo-600">{{ $q->answers_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">回答</div>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="text-2xl font-bold text-gray-600">{{ $q->views_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">閲覧</div>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    @if($q->is_resolved)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            解決済み
                                        </span>
                                    @endif
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-medium rounded-full">{{ $q->category ?? 'その他' }}</span>
                                    @foreach($q->tags->take(3) as $tag)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 mb-2 hover:text-indigo-600 transition-colors">{{ $q->title }}</h2>
                                <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit($q->content, 150) }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        @php $authorF = $q->user?->freelancer; @endphp
                                        <img src="{{ $authorF?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                        <div class="font-medium text-sm text-gray-900">{{ $authorF?->display_name ?? $q->user?->email ?? '匿名' }}</div>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $q->created_at?->format('Y/m/d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
