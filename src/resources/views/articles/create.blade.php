@extends('layouts.public')

@section('title', '記事を投稿 - AIスキルマッチ')

@push('styles')
<style>
    /* 本文はシンプルなtextareaにするため、Quill関連スタイルは使いません */
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                記事一覧に戻る
            </a>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <h1 class="text-4xl font-bold text-gray-900">記事を投稿</h1>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" form="articleForm" class="flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        投稿する
                    </button>
                </div>
            </div>
        </div>

        <form id="articleForm" action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('partials.error-panel')

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">基本情報</h2>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        タイトル <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="記事のタイトルを入力してください" maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        概要 <span class="text-red-500">*</span>
                    </label>
                    <textarea name="excerpt" id="excerpt" placeholder="記事の概要を入力してください（2-3文程度）" rows="3" maxlength="200"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none @error('excerpt') border-red-500 @enderror">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        カテゴリー <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('category') border-red-500 @enderror">
                        <option value="">選択してください</option>
                        <option value="n8n" {{ old('category') === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="AIツール" {{ old('category') === 'AIツール' ? 'selected' : '' }}>AIツール</option>
                        <option value="自動化" {{ old('category') === '自動化' ? 'selected' : '' }}>自動化</option>
                        <option value="プログラミング" {{ old('category') === 'プログラミング' ? 'selected' : '' }}>プログラミング</option>
                        <option value="ビジネス活用" {{ old('category') === 'ビジネス活用' ? 'selected' : '' }}>ビジネス活用</option>
                        <option value="副業・フリーランス" {{ old('category') === '副業・フリーランス' ? 'selected' : '' }}>副業・フリーランス</option>
                        <option value="その他" {{ old('category', '') === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    @php
                        $tagSlots = old('tags', []);
                        if (!is_array($tagSlots)) {
                            $tagSlots = $tagSlots ? [$tagSlots] : [];
                        }
                        $tagSlots = array_values($tagSlots);
                        $tagSlots = array_slice($tagSlots, 0, 16);

                        $minSlots = 4;
                        $maxSlots = 16;
                        $styleRows = (int) max(1, ceil(max(count($tagSlots), $minSlots) / 4));
                        $styleRows = min(4, $styleRows);
                        $tagSlots = array_pad($tagSlots, $styleRows * 4, '');
                    @endphp

                    <label class="block text-sm font-semibold text-gray-700 mb-2">タグ</label>

                    <div id="article-tag-items-container" class="space-y-3">
                        @for($row = 0; $row < $styleRows; $row++)
                            <div class="article-tag-input-row grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @for($col = 0; $col < 4; $col++)
                                    @php $idx = $row * 4 + $col; @endphp
                                    <input
                                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        name="tags[]"
                                        type="text"
                                        value="{{ $tagSlots[$idx] ?? '' }}"
                                        placeholder="例: API"
                                    >
                                @endfor
                            </div>
                        @endfor
                    </div>

                    <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
                        <button type="button" id="add-article-tags-row" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                            追加
                        </button>
                        <button type="button" id="remove-article-tags-row" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors" aria-label="タグ入力行を減らす">
                            ×
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 mt-2">1行4件で入力できます（4〜16件）</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        本文 <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="body_html"
                        name="body_html"
                        rows="10"
                        maxlength="50000"
                        placeholder="本文を入力してください"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent resize-vertical @error('body_html') border-red-500 ring-2 ring-red-100 @enderror"
                    >{{ old('body_html') }}</textarea>
                    @error('body_html')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">アイキャッチ画像（任意）</label>
                    <div class="flex gap-2 items-center">
                        <input type="file" name="eyecatch_image" id="eyecatchImage" accept="image/*"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('eyecatch_image') border-red-500 @enderror">
                        <button type="button" id="removeEyecatchBtn" class="px-3 py-2 border rounded-lg text-sm text-gray-600">削除</button>
                    </div>
                    <div id="imagePreviewContainer" class="mt-3"></div>
                    @error('eyecatch_image')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">公開設定</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_published" value="1" checked class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="text-gray-700">
                                <span class="font-medium">公開</span>
                                <span class="text-sm text-gray-500 ml-1">（誰でも閲覧できます）</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_published" value="0" class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="text-gray-700">
                                <span class="font-medium">非公開</span>
                                <span class="text-sm text-gray-500 ml-1">（自分のみ閲覧できます）</span>
                            </span>
                        </label>
                    </div>
                    @error('is_published')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('articles.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                    投稿する
                </button>
            </div>
        </form>
    </div>
</div>

{{-- プレビュー機能（Quill依存）のため、いったん非表示にします --}}
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tagItemsContainer = document.getElementById('article-tag-items-container');
        const addTagRowBtn = document.getElementById('add-article-tags-row');
        const removeTagRowBtn = document.getElementById('remove-article-tags-row');

        const MAX_ROWS = 4; // 16 slots (4 inputs per row)
        const MIN_ROWS = 1; // 4 slots  (1 row)

        function buildTagRow() {
            const row = document.createElement('div');
            row.className = 'article-tag-input-row grid grid-cols-2 sm:grid-cols-4 gap-3';

            for (let col = 0; col < 4; col++) {
                const input = document.createElement('input');
                input.className = 'w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent';
                input.name = 'tags[]';
                input.type = 'text';
                input.placeholder = '例: API';
                row.appendChild(input);
            }
            return row;
        }

        function syncTagRowButtons() {
            if (!tagItemsContainer || !addTagRowBtn || !removeTagRowBtn) return;
            const rowCount = tagItemsContainer.querySelectorAll('.article-tag-input-row').length;
            addTagRowBtn.disabled = rowCount >= MAX_ROWS;
            removeTagRowBtn.disabled = rowCount <= MIN_ROWS;
            addTagRowBtn.setAttribute('aria-disabled', String(rowCount >= MAX_ROWS));
            removeTagRowBtn.setAttribute('aria-disabled', String(rowCount <= MIN_ROWS));
        }

        if (tagItemsContainer && addTagRowBtn && removeTagRowBtn) {
            addTagRowBtn.addEventListener('click', function () {
                const rowCount = tagItemsContainer.querySelectorAll('.article-tag-input-row').length;
                if (rowCount >= MAX_ROWS) return;
                tagItemsContainer.appendChild(buildTagRow());
                syncTagRowButtons();
            });

            removeTagRowBtn.addEventListener('click', function () {
                const rows = tagItemsContainer.querySelectorAll('.article-tag-input-row');
                if (rows.length <= MIN_ROWS) return;
                const last = rows[rows.length - 1];
                if (last) last.remove();
                syncTagRowButtons();
            });

            syncTagRowButtons();
        }

        // アイキャッチ画像ファイルのクライアントプレビュー（FileReader）
        const eyecatchInput = document.getElementById('eyecatchImage');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const removeEyecatchBtn = document.getElementById('removeEyecatchBtn');
        if (eyecatchInput && imagePreviewContainer) {
            eyecatchInput.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                imagePreviewContainer.innerHTML = '';
                if (!file) return;
                if (!file.type.startsWith('image/')) {
                    imagePreviewContainer.innerHTML = '<p class=\"text-red-500 text-sm mt-3\">画像ファイルを選択してください</p>';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (ev) {
                    imagePreviewContainer.innerHTML = '<div class=\"mt-3 rounded-xl overflow-hidden\"><img src=\"' + ev.target.result + '\" alt=\"Preview\" class=\"w-full h-48 object-cover\"></div>';
                };
                reader.readAsDataURL(file);
            });
        }
        if (removeEyecatchBtn) {
            removeEyecatchBtn.addEventListener('click', function () {
                if (eyecatchInput) eyecatchInput.value = '';
                if (imagePreviewContainer) imagePreviewContainer.innerHTML = '';
            });
        }
    });
</script>
@endpush
