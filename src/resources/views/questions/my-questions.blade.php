@extends('layouts.public')

@section('title', '自分の質問一覧 - AI知恵袋')

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
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">自分の質問一覧</h1>
                    <p class="text-gray-600">作成した質問を一覧で確認できます</p>
                </div>
                <a href="{{ route('questions.index') }}"
                   class="flex items-center gap-2 px-6 py-4 border-2 border-gray-200 text-gray-700 rounded-xl font-bold shadow-sm hover:bg-gray-50 transition-all duration-300 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    質問一覧に戻る
                </a>
            </div>
        </div>

        @if($questions->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl shadow">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-16 h-16 text-gray-300 mx-auto mb-4">
                    <path d="M20 21V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v13"/>
                    <path d="M20 21H4"/>
                    <path d="M9 21V11h6v10"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">まだ質問がありません</h3>
                <p class="text-gray-600 mb-6">最初の質問を投稿してみましょう</p>
                <a href="{{ route('questions.create') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                    質問を投稿する
                </a>
            </div>
        @else
            <div class="space-y-4">
                @php
                    $viewerId = null;
                    if(auth('freelancer')->check()) {
                        $viewerId = auth('freelancer')->user()->id;
                    } elseif(auth('company')->check()) {
                        $viewerId = auth('company')->user()->id;
                    }
                @endphp

                @foreach($questions as $q)
                    <div class="block bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6">
                        <a href="{{ route('questions.show', ['question' => $q->id]) }}" class="block">
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
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3 h-3">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
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

                        @if($viewerId && (int)$viewerId === (int)$q->user_id)
                            <div class="mt-4 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                                <button type="button"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors"
                                        onclick="openMyQuestionDeleteModal({{ json_encode($q->title) }}, {{ json_encode(route('questions.destroy', ['question' => $q->id])) }});">
                                    削除
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>

<div id="myQuestionDeleteModal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-2">質問を削除しますか？</h3>
        <p id="myQuestionDeleteModalMessage" class="text-sm text-gray-600 mb-6">この操作は取り消せません。</p>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeMyQuestionDeleteModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">キャンセル</button>
            <form id="myQuestionDeleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700">削除する</button>
            </form>
        </div>
    </div>
</div>

<script>
    let currentMyQuestionDeleteUrl = null;

    function openMyQuestionDeleteModal(questionTitle, destroyUrl) {
        currentMyQuestionDeleteUrl = destroyUrl;
        const modal = document.getElementById('myQuestionDeleteModal');
        const message = document.getElementById('myQuestionDeleteModalMessage');
        const form = document.getElementById('myQuestionDeleteForm');
        if (!modal || !message || !form) return;

        message.textContent = '「' + questionTitle + '」を本当に削除しますか？この操作は取り消せません。';
        form.action = currentMyQuestionDeleteUrl;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeMyQuestionDeleteModal() {
        const modal = document.getElementById('myQuestionDeleteModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        currentMyQuestionDeleteUrl = null;
    }

    document.getElementById('myQuestionDeleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeMyQuestionDeleteModal();
    });
</script>
@endsection

