@extends('layouts.public')

@section('title', ($question->title ?? '質問') . ' - AI知恵袋')

@push('styles')
<style>
.prose p { margin-bottom: 1rem; line-height: 1.75; }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('questions.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                質問一覧に戻る
            </a>
        <div class="flex flex-wrap items-center gap-3">
            @php
                $viewerId = null;
                if(auth('freelancer')->check()) {
                    $viewerId = auth('freelancer')->user()->id;
                } elseif(auth('company')->check()) {
                    $viewerId = auth('company')->user()->id;
                }
                $canDelete = $viewerId && (int)$viewerId === (int)$question->user_id;
            @endphp

            @if(auth('freelancer')->check() || auth('company')->check())
                <a href="{{ route('questions.my.index') }}" class="flex items-center gap-2 px-6 py-4 border-2 border-indigo-200 text-indigo-700 rounded-xl font-bold shadow-sm hover:bg-indigo-50 transition-all duration-300 text-lg">
                    自分の質問一覧
                </a>
            @endif

            <a href="{{ route('questions.create') }}" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                質問を投稿
            </a>

            @if($canDelete)
                <button type="button"
                        onclick="openQuestionDeleteModal('{{ route('questions.destroy', ['question' => $question->id]) }}')"
                        class="flex items-center gap-2 px-6 py-3 border-2 border-red-200 text-red-700 rounded-xl font-bold shadow-sm hover:bg-red-50 transition-all duration-300">
                    削除
                </button>
            @endif
        </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-full">{{ $question->category ?? 'その他' }}</span>
                @foreach($question->tags as $tag)
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#{{ $tag->name }}</span>
                @endforeach
                <div class="ml-auto flex items-center gap-4 text-sm text-gray-500">
                    <span>{{ $question->views_count ?? 0 }} 閲覧</span>
                    <span>{{ $question->answers_count ?? 0 }} 回答</span>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $question->title }}</h1>

            <div class="prose max-w-none mb-6">
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $question->content }}</p>
            </div>

            @php $authorF = $question->user?->freelancer; @endphp
            <div class="flex items-center gap-3 pt-6 border-t border-gray-200">
                <img src="{{ $authorF?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="w-12 h-12 rounded-full object-cover">
                <div>
                    <div class="font-medium text-gray-900">{{ $authorF?->display_name ?? $question->user?->email ?? '匿名' }}</div>
                    <div class="text-sm text-gray-500">{{ $question->created_at?->format('Y/m/d H:i') }}</div>
                </div>
            </div>
        </div>

        @if($question->ai_answer)
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-lg p-8 mb-6 border border-purple-200">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-6 h-6 text-white"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">AIによる参考回答</h3>
                    <p class="text-sm text-gray-600">この回答はAIによって自動生成されています</p>
                </div>
            </div>
            <div class="prose max-w-none">
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $question->ai_answer }}</p>
            </div>
        </div>
        @endif

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $question->answers->count() }}件の回答</h2>

            @if($question->answers->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <p class="text-gray-600">まだ回答がありません。最初の回答を投稿してみましょう。</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($question->answers as $answer)
                        <div class="bg-white rounded-xl shadow-md p-6 {{ $question->accepted_answer_id === $answer->id ? 'ring-2 ring-green-500 ring-offset-2' : '' }}">
                            @if($question->accepted_answer_id === $answer->id)
                                <div class="flex items-center gap-2 text-green-600 mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <span class="font-semibold">ベストアンサー</span>
                                </div>
                            @endif
                            <div class="prose max-w-none mb-4">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $answer->content }}</p>
                            </div>
                            @php $answerAuthor = $answer->user?->freelancer; @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $answerAuthor?->icon_path ?? 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop' }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                    <div>
                                        <div class="font-medium text-sm text-gray-900">{{ $answerAuthor?->display_name ?? $answer->user?->email ?? '匿名' }}</div>
                                        <div class="text-xs text-gray-500">{{ $answer->created_at?->format('Y/m/d H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(auth('freelancer')->check() || auth('company')->check())
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl shadow-lg p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">回答を投稿する</h3>
            <form action="{{ route('questions.answers.store', $question) }}" method="POST" class="space-y-4">
                @csrf
                @include('partials.error-panel')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">回答内容 <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="6" maxlength="5000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('content') border-red-500 @enderror"
                        placeholder="回答を入力してください">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">5000文字以内</p>
                </div>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">回答を投稿</button>
            </form>
        </div>
        @else
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl shadow-lg p-8 text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">回答を投稿するにはログインが必要です</h3>
            <p class="text-gray-600 mb-6">ログインしてコミュニティに貢献しましょう</p>
            <a href="{{ route('auth.login.form') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">ログイン</a>
        </div>
        @endif
    </div>
</div>

@if($canDelete ?? false)
    <div id="questionDeleteModal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4 bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">質問を削除しますか？</h3>
            <p class="text-sm text-gray-600 mb-6">この操作は取り消せません。</p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeQuestionDeleteModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">キャンセル</button>
                <form id="questionDeleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700">削除する</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentQuestionDeleteUrl = null;
        function openQuestionDeleteModal(destroyUrl) {
            currentQuestionDeleteUrl = destroyUrl;
            const modal = document.getElementById('questionDeleteModal');
            const form = document.getElementById('questionDeleteForm');
            if (!modal || !form) return;
            form.action = currentQuestionDeleteUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeQuestionDeleteModal() {
            const modal = document.getElementById('questionDeleteModal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            currentQuestionDeleteUrl = null;
        }
        document.getElementById('questionDeleteModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeQuestionDeleteModal();
        });
    </script>
@endif
@endsection
