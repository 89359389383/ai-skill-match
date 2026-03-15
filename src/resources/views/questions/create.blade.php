@extends('layouts.public')

@section('title', '質問を投稿 - AI知恵袋')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">質問を投稿</h1>

        <form action="{{ route('questions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-500 @enderror"
                        placeholder="質問のタイトルを入力してください">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">内容 <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="8" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('content') border-red-500 @enderror"
                        placeholder="質問の内容を詳しく入力してください">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">カテゴリー</label>
                    <select name="category"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('category') border-red-500 @enderror">
                        <option value="">選択してください</option>
                        <option value="n8n" {{ old('category') === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="AIツール" {{ old('category') === 'AIツール' ? 'selected' : '' }}>AIツール</option>
                        <option value="自動化" {{ old('category') === '自動化' ? 'selected' : '' }}>自動化</option>
                        <option value="プログラミング" {{ old('category') === 'プログラミング' ? 'selected' : '' }}>プログラミング</option>
                        <option value="ビジネス活用" {{ old('category') === 'ビジネス活用' ? 'selected' : '' }}>ビジネス活用</option>
                        <option value="副業・フリーランス" {{ old('category') === '副業・フリーランス' ? 'selected' : '' }}>副業・フリーランス</option>
                        <option value="その他" {{ old('category') === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タグ（カンマ区切り）</label>
                    <input type="text" name="tags_input" value="{{ is_array(old('tags')) ? implode(',', old('tags')) : old('tags_input') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="例: API, 業務効率化, 導入事例">
                    <p class="mt-1 text-xs text-gray-500">カンマで区切って複数入力できます</p>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('questions.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    投稿する
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
        const tags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);
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
