@extends('layouts.public')

@section('title', '記事を編集 - AIスキルマッチ')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('my-articles.show', ['article' => $article->id]) }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="font-medium">記事詳細に戻る</span>
            </a>

            <h1 class="text-4xl font-bold text-gray-900">記事を編集</h1>
        </div>

        <form action="{{ route('my-articles.update', ['article' => $article->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('partials.error-panel')

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">基本情報</h2>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror"
                        placeholder="記事のタイトルを入力してください">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">概要 <span class="text-red-500">*</span></label>
                    <textarea name="excerpt" rows="3" maxlength="200"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('excerpt') border-red-500 @enderror"
                        placeholder="記事の概要を入力してください">{{ old('excerpt', $article->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">カテゴリー <span class="text-red-500">*</span></label>
                    <select name="category"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-500 @enderror">
                        <option value="ChatGPT" {{ old('category', $article->category) === 'ChatGPT' ? 'selected' : '' }}>ChatGPT</option>
                        <option value="n8n" {{ old('category', $article->category) === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="Python" {{ old('category', $article->category) === 'Python' ? 'selected' : '' }}>Python</option>
                        <option value="その他" {{ old('category', $article->category) === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タグ（カンマ区切り、最大5個）</label>
                    @php
                        $tagNames = $article->tags->pluck('name')->toArray();
                        $tagsValue = implode(',', old('tags', $tagNames));
                    @endphp
                    <input type="text" name="tags_input" value="{{ $tagsValue }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="例: ChatGPT, 業務効率化, AI活用">
                    @error('tags')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">アイキャッチ画像URL</label>
                    <input type="url" name="eyecatch_image_url" value="{{ old('eyecatch_image_url', $article->eyecatch_image_url) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="https://example.com/image.jpg">
                    @error('eyecatch_image_url')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">記事の構成（目次）</h2>
                <p class="text-sm text-gray-600">記事の構造はそのまま保持されます。高度な編集は今後対応予定です。</p>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('my-articles.show', ['article' => $article->id]) }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    更新する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const tagsInput = document.querySelector('input[name="tags_input"]');
    if (tagsInput && tagsInput.value.trim()) {
        const tags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t).slice(0, 5);
        const hidden = document.createElement('div');
        tags.forEach((tag, i) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[' + i + ']';
            input.value = tag;
            hidden.appendChild(input);
        });
        this.appendChild(hidden);
    }
});
</script>
@endpush
