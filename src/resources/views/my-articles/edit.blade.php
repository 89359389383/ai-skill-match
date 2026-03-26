@extends('layouts.public')

@section('title', '記事を編集 - AIスキルマッチ')

@push('styles')
<style>
    /* 本文はシンプルなtextareaにするため、Quill関連スタイルは使いません */
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('my-articles.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="font-medium">投稿記事一覧に戻る</span>
            </a>

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-4xl font-bold text-gray-900">記事を編集</h1>
                <button type="button" onclick="handlePreview()" class="flex items-center gap-2 px-4 py-2 border-2 border-green-600 text-green-600 rounded-xl font-semibold hover:bg-green-50 transition-all">
                    プレビュー
                </button>
            </div>
        </div>

        <form id="articleForm" action="{{ route('my-articles.update', ['article' => $article->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('partials.error-panel')

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">基本情報</h2>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" maxlength="255"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('title') border-red-500 @enderror"
                        placeholder="記事のタイトルを入力してください">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">概要 <span class="text-red-500">*</span></label>
                    <textarea name="excerpt" id="excerpt" rows="3" maxlength="200"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent @error('excerpt') border-red-500 @enderror"
                        placeholder="記事の概要を入力してください">{{ old('excerpt', $article->excerpt) }}</textarea>
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
                        <option value="n8n" {{ old('category', $article->category) === 'n8n' ? 'selected' : '' }}>n8n</option>
                        <option value="AIツール" {{ old('category', $article->category) === 'AIツール' ? 'selected' : '' }}>AIツール</option>
                        <option value="自動化" {{ old('category', $article->category) === '自動化' ? 'selected' : '' }}>自動化</option>
                        <option value="プログラミング" {{ old('category', $article->category) === 'プログラミング' ? 'selected' : '' }}>プログラミング</option>
                        <option value="ビジネス活用" {{ old('category', $article->category) === 'ビジネス活用' ? 'selected' : '' }}>ビジネス活用</option>
                        <option value="副業・フリーランス" {{ old('category', $article->category) === '副業・フリーランス' ? 'selected' : '' }}>副業・フリーランス</option>
                        <option value="その他" {{ old('category', $article->category) === 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    @php
                        $tagSlots = old('tags', $article->tags->pluck('name')->toArray());
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
                    >{{ old('body_html', filled($article->body_html) ? $article->body_html : $article->editorInitialHtml()) }}</textarea>
                    @error('body_html')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">アイキャッチ画像（任意）</label>
                    <div id="imagePreview" style="display: none;" class="relative mb-4">
                        <img id="previewImg" src="" alt="Preview" class="w-full aspect-video object-cover rounded-lg">
                        <button type="button" onclick="removeImage()" class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <label id="uploadLabel" class="flex flex-col items-center justify-center w-full aspect-video border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition-all bg-gray-50">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">クリックして画像をアップロード</span></p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF</p>
                        </div>
                        <input type="file" id="imageInput" name="eyecatch_image" class="hidden" accept="image/*" onchange="handleImageUpload(event)">
                    </label>
                    @error('eyecatch_image')
                        <p class="mt-1 text-sm text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">公開設定</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_published" value="1" {{ old('is_published', $article->published_at ? '1' : '0') == '1' ? 'checked' : '' }} class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="text-gray-700">
                                <span class="font-medium">公開</span>
                                <span class="text-sm text-gray-500 ml-1">（誰でも閲覧できます）</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_published" value="0" {{ old('is_published', $article->published_at ? '1' : '0') == '0' ? 'checked' : '' }} class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
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

            <div class="flex flex-wrap justify-end gap-4 pt-2">
                <a href="{{ route('my-articles.index') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all">
                    キャンセル
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                    更新
                </button>
            </div>
        </form>
    </div>
</div>

<div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" onclick="closePreview()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">プレビュー</h3>
                <button type="button" onclick="closePreview()" class="p-2 text-gray-500 hover:text-gray-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="previewContent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var existingEyecatchUrl = @json($article->eyecatch_image_url);
    var currentEyecatchUrl = existingEyecatchUrl;

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

        // 既存のアイキャッチ画像を、skills/create と同じDOMに反映
        var imageInput = document.getElementById('imageInput');
        var imagePreview = document.getElementById('imagePreview');
        var previewImg = document.getElementById('previewImg');
        var uploadLabel = document.getElementById('uploadLabel');

        function setPreviewFromUrl(url) {
            if (!imagePreview || !previewImg || !uploadLabel || !url) return;
            previewImg.src = url;
            imagePreview.style.display = 'block';
            uploadLabel.style.display = 'none';
            currentEyecatchUrl = url;
        }

        if (existingEyecatchUrl && imagePreview && previewImg && uploadLabel && (!imageInput || !imageInput.files || !imageInput.files.length)) {
            setPreviewFromUrl(existingEyecatchUrl);
        }
    });

    function escapeAttr(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function buildPreviewHtml(imageUrl) {
        var title = document.getElementById('title').value;
        var excerpt = document.getElementById('excerpt').value;
        var category = document.getElementById('category').value;
        var bodyHtml = document.getElementById('body_html').value;

        var html = '';
        if (imageUrl) html += '<div class="mb-6"><img src="' + escapeAttr(imageUrl) + '" alt="" class="w-full h-48 object-cover rounded-xl"></div>';
        html += '<h1 class="text-2xl font-bold text-gray-900 mb-4">' + escapeHtml(title || '（タイトル未入力）') + '</h1>';
        html += '<p class="text-gray-600 mb-4">' + escapeHtml(excerpt || '（概要未入力）') + '</p>';
        html += '<span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm mb-4">' + escapeHtml(category || '（カテゴリー未選択）') + '</span>';
        var currentTags = Array.from(document.querySelectorAll('input[name="tags[]"]'))
            .map(function(el) { return (el.value || '').trim(); })
            .filter(function(v) { return v.length > 0; });
        html += '<div class="flex flex-wrap gap-2 mb-6">' + currentTags.map(function(t) {
            return '<span class="px-3 py-1 bg-gray-100 rounded-full text-sm">#' + escapeHtml(t) + '</span>';
        }).join('') + '</div>';
        html += '<div class="article-body-preview text-gray-800 text-base leading-relaxed">' + bodyHtml + '</div>';
        return html;
    }

    function handlePreview() {
        var fileInput = document.getElementById('imageInput');
        if (fileInput && fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                currentEyecatchUrl = ev.target.result;
                document.getElementById('previewContent').innerHTML = buildPreviewHtml(ev.target.result);
                document.getElementById('previewModal').classList.remove('hidden');
            };
            reader.readAsDataURL(fileInput.files[0]);
            return;
        }

        document.getElementById('previewContent').innerHTML = buildPreviewHtml(currentEyecatchUrl || '');
        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }

    // skills/create と同等のプレビュー制御（編集画面でも削除・再選択できるように）
    function handleImageUpload(event) {
        var file = event && event.target && event.target.files ? event.target.files[0] : null;
        if (!file) return;
        if (!file.type || !file.type.startsWith('image/')) return;

        var reader = new FileReader();
        reader.onload = function(e) {
            currentEyecatchUrl = e.target.result;

            var previewImg = document.getElementById('previewImg');
            var imagePreview = document.getElementById('imagePreview');
            var uploadLabel = document.getElementById('uploadLabel');

            if (previewImg) previewImg.src = currentEyecatchUrl;
            if (imagePreview) imagePreview.style.display = 'block';
            if (uploadLabel) uploadLabel.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    function removeImage() {
        currentEyecatchUrl = '';

        var previewImg = document.getElementById('previewImg');
        var imagePreview = document.getElementById('imagePreview');
        var uploadLabel = document.getElementById('uploadLabel');
        var input = document.getElementById('imageInput');

        if (previewImg) previewImg.src = '';
        if (imagePreview) imagePreview.style.display = 'none';
        if (uploadLabel) uploadLabel.style.display = 'flex';
        if (input) input.value = '';
    }
</script>
@endpush
