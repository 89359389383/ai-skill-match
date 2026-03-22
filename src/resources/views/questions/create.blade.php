@extends('layouts.public')

@section('title', '質問を投稿 - AI知恵袋')

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">質問を投稿</h1>

        @include('partials.error-panel')

        <form action="{{ route('questions.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-500 @enderror"
                        placeholder="質問のタイトルを入力してください">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">内容 <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="8"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('content') border-red-500 @enderror"
                        placeholder="質問の内容を詳しく入力してください">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
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
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タグ</label>
                    <p class="mb-2 text-xs text-gray-500">複数入力できます</p>
                    @php
                        $tagsInvalid = $errors->has('tags') || $errors->has('tags.*');
                        $oldTags = array_slice(old('tags', []), 0, 10);
                        $displayTags = count($oldTags) >= 4 ? $oldTags : array_merge($oldTags, array_fill(0, max(0, 4 - count($oldTags)), ''));
                        $tagRows = array_chunk($displayTags, 2);
                    @endphp
                    <div class="space-y-3" id="tags-container">
                        @foreach($tagRows as $rowTags)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 tag-row">
                            @foreach($rowTags as $tagVal)
                            <input class="tag-input px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @if($tagsInvalid) border-red-500 @endif" name="tags[]" type="text" value="{{ $tagVal }}" placeholder="例: API">
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-2 flex gap-2 flex-wrap">
                        <button type="button" class="px-4 py-2 border-2 border-orange-500 text-orange-500 rounded-xl font-semibold hover:bg-orange-50 transition-all" id="add-tag-btn">追加する</button>
                        <button type="button" class="px-4 py-2 border-2 border-orange-500 text-orange-500 rounded-xl font-semibold hover:bg-orange-50 transition-all" id="remove-tag-btn">削除する</button>
                    </div>
                    @if($errors->has('tags') || $errors->has('tags.*'))
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $errors->first('tags') ?: $errors->first('tags.*') }}</p>
                    @endif
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('questions.index') }}" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all text-center">
                    キャンセル
                </a>
                <button type="submit" class="flex-1 px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    投稿する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const MIN_TAGS = 4;
    const MAX_TAGS = 10;
    const addTagBtn = document.getElementById('add-tag-btn');
    const removeTagBtn = document.getElementById('remove-tag-btn');
    const tagsContainer = document.getElementById('tags-container');

    function getTagCount() {
        return tagsContainer ? tagsContainer.querySelectorAll('.tag-input').length : 0;
    }

    function updateButtons() {
        const count = getTagCount();
        if (addTagBtn) {
            addTagBtn.disabled = count >= MAX_TAGS;
            addTagBtn.classList.toggle('opacity-50', count >= MAX_TAGS);
            addTagBtn.classList.toggle('cursor-not-allowed', count >= MAX_TAGS);
        }
        if (removeTagBtn) {
            removeTagBtn.disabled = count <= MIN_TAGS;
            removeTagBtn.classList.toggle('opacity-50', count <= MIN_TAGS);
            removeTagBtn.classList.toggle('cursor-not-allowed', count <= MIN_TAGS);
        }
    }

    if (addTagBtn && removeTagBtn && tagsContainer) {
        addTagBtn.addEventListener('click', function() {
            if (getTagCount() >= MAX_TAGS) return;

            const lastRow = tagsContainer.querySelector('.tag-row:last-of-type');
            const inputsInLastRow = lastRow ? lastRow.querySelectorAll('.tag-input') : [];
            const inputClass = 'tag-input px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent';

            if (lastRow && inputsInLastRow.length < 2) {
                const newInput = document.createElement('input');
                newInput.className = inputClass;
                newInput.name = 'tags[]';
                newInput.type = 'text';
                newInput.placeholder = '例: タグ名';
                lastRow.appendChild(newInput);
            } else {
                const newRow = document.createElement('div');
                newRow.className = 'grid grid-cols-1 sm:grid-cols-2 gap-3 tag-row';
                const newInput = document.createElement('input');
                newInput.className = inputClass;
                newInput.name = 'tags[]';
                newInput.type = 'text';
                newInput.placeholder = '例: タグ名';
                newRow.appendChild(newInput);
                tagsContainer.appendChild(newRow);
            }
            updateButtons();
        });

        removeTagBtn.addEventListener('click', function() {
            if (getTagCount() <= MIN_TAGS) return;

            const inputs = tagsContainer.querySelectorAll('.tag-input');
            const lastInput = inputs[inputs.length - 1];
            const parentRow = lastInput.closest('.tag-row');
            lastInput.remove();
            if (parentRow && parentRow.querySelectorAll('.tag-input').length === 0) {
                parentRow.remove();
            }
            updateButtons();
        });

        updateButtons();
    }
})();
</script>
@endpush
